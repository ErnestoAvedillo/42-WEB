#! /usr/bin/env python

from urllib import response
from fastapi import FastAPI, Request, File, UploadFile
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import List,Optional
from PIL import Image
import base64
import os
import io
import time
import html
import json
import ast
import re

Log_file = "/var/log/app.log"

def fix_base64_padding(encoded_string):
    missing_padding = len(encoded_string) % 4
    if missing_padding:
        encoded_string += '=' * (4 - missing_padding)
    return encoded_string

app = FastAPI()

from fastapi.exceptions import RequestValidationError
from fastapi.encoders import jsonable_encoder


@app.exception_handler(RequestValidationError)
async def validation_exception_handler(request, exc: RequestValidationError):
    # Sanitize errors to avoid UnicodeDecodeError when bytes contain non-UTF8 data
    def sanitize(obj):
        if isinstance(obj, dict):
            return {k: sanitize(v) for k, v in obj.items()}
        if isinstance(obj, list):
            return [sanitize(v) for v in obj]
        if isinstance(obj, bytes):
            try:
                return obj.decode('utf-8')
            except Exception:
                return base64.b64encode(obj).decode('ascii')
        return obj

    safe = sanitize(exc.errors())
    return JSONResponse(status_code=422, content=jsonable_encoder({"detail": safe}))

class InputData(BaseModel):
    picture: Optional[str] = None
    factura: Optional[str] = None

class Factura(BaseModel):
    fecha: Optional[str] = None
    importe: str
    concepto: str
    vencimiento: Optional[str] = None
    document_uuid: str

class Contacto(BaseModel):
    nombre: str
    cif: str
    domicilio: str
    telefono: str
    fax: str
    email: str

class ConceptoData(BaseModel):
    acreedor: Contacto
    deudor: Contacto
    lista_facturas: dict[str, Factura]

@app.post("/autofill")
async def autofill(data: InputData):
    import google.generativeai as genai
    if Log_file and os.path.exists(Log_file):
        os.remove(Log_file)
    
    # models = genai.list_models()
    # with open(Log_file, "a") as f:
    #     for model in models:
    #         f.write(f"Model: {model.name}, Supported Methods: {model.supported_generation_methods}\n")

    try:
        if not data.picture:
            return {"success": False, "error": "No picture provided"}

        if ',' in data.picture:
            header, encoded_data = data.picture.split(',', 1)
        else:
            raise ValueError("Invalid picture format: Missing header")
        
        # Decode the header to get the MIME type
        mime_type = header.split(';')[0].split(':')[1] if ':' in header else None

        # Fix Base64 padding
        fixed_picture = fix_base64_padding(encoded_data)

        image_data = base64.b64decode(fixed_picture)

        image_json = {"mime_type": mime_type, "data": image_data}

        # Validate the image data
        image = Image.open(io.BytesIO(image_data))
        image.verify()  # Verify that it is a valid image
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error processing images: {str(e)}\n")
        return {"success": False, "error": f"Invalid image format or data: {str(e)}"}

    try:
        GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        genai.configure(api_key=GOOGLE_API_KEY)

        prompt = "Return only one funny caption for this picture"
        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')

        response = model.generate_content([prompt, image_json])
        with open(Log_file, "a") as f:
            f.write(f"Prompt sent to Gemini API: {prompt}\n")
        text = response.text.replace("\"", "")
        with open(Log_file, "a") as f:
            f.write(f"Response from Gemini API: {response}\n")
        return {"success": True, "caption": text}
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return {"success": False, "error": f"Error from Gemini API: {str(e)}"}


@app.post("/factura")
async def factura(factura: UploadFile = File(...)):
    import google.generativeai as genai
    if Log_file and os.path.exists(Log_file):
        os.remove(Log_file)
    with open( Log_file,"a") as f:
        f.write(f"Function called {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
    # Leer el contenido una sola vez
    file_bytes = await factura.read()
    # with open( Log_file,"a") as f:
    #         f.write(f"filename: {factura.filename}: {file_bytes[0:50]}  \n data--end\n")
    #         f.write(f"file size: {factura.size} bytes\n")
    #         f.write(f"file mime type: {factura.content_type}\n")
    try:
        image_json = {"mime_type": factura.content_type, "data": file_bytes}
    except Exception as e:
        with open("/var/log/app.log", "a") as f:
            f.write(f"Error procesando fichero factura: {str(e)}\n")
        return JSONResponse(status_code=400, content={"success": False, "error": f"Invalid file format or data: {str(e)}"})

    try:
        GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        genai.configure(api_key=GOOGLE_API_KEY)

        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')
        prompt = 'Devuelve la siguiente información de este documento en un archivo JSON'
        prompt += 'Las fechas las debes devolver en formato d/m/Y'
        prompt += '{"acreedor":{"nombre":"","CIF":"","domicilio":"","telefono":"","FAX":"","email":""}}'
        prompt += '{"deudor":{"nombre":"","CIF":"","domicilio":"","telefono":"","FAX":"","email":""}}'
        prompt += '{"factura":{"numero":"","fecha":"","vencimiento":"","importe_total":"","importe_iva":"","importe_base":""}}'
        prompt += '{"concepto":""}'

        response = model.generate_content([prompt, image_json])
        with open("/var/log/app.log", "a") as f:
            f.write(f"Prompt sent to Gemini API: {prompt}\n")
        text = response.text.replace("\"", "")
        with open("/var/log/app.log", "a") as f:
            f.write(f"Response from Gemini API: {response}\n")
        clean_str = re.sub(r"^```json\s*|\s*```$", "", response.text, flags=re.DOTALL)
        json_data = json.loads(clean_str)
    except Exception as e:
        with open("/var/log/app.log", "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return JSONResponse(status_code=400, content={"success": False, "error": f"Error from Gemini API: {str(e)}"})
    return JSONResponse(status_code=200, content={"success": True, "caption": json_data})

@app.get("/factura")
async def factura_info():
    # Helpful response for browser GETs or accidental visits
    return JSONResponse(status_code=405, content={
        "success": False,
        "error": "Method Not Allowed. Use POST multipart/form-data to upload a file to /factura."
    })

@app.post("/concepto")
async def concepto(data: ConceptoData):
    import google.generativeai as genai
    if Log_file and os.path.exists(Log_file):
        os.remove(Log_file)
    with open(Log_file, "a") as f:
        f.write(f"Function called {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
    try:
        GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        genai.configure(api_key=GOOGLE_API_KEY)

        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')
        prompt = 'Eres un asistente jurídico. Te paso un JSON con facturas impagadas (cada objeto contiene: número de factura, fecha de emisión, fecha de vencimiento, concepto).'
        prompt += 'Con esa información, redacta el un escrito de demanda de reclamación de cantidad en España para presentar ante el Juzgado de Primera Instancia.'
        prompt += 'El escrito solo debe incluir los apartados:'
        prompt += 'ORIGEN DE LA DEUDA: describiendo la relación contractual y detallando cada factura (número, fecha, vencimiento, concepto, importe).'
        prompt += 'SOLICITO AL JUZGADO:.'
        prompt += 'Importante: redacta el texto en formato claro y profesional, conciso y directo, evitando ambigüedades listo para adaptar y presentar en el juzgado.'
        prompt += 'No inventes datos de las partes; deja campos genéricos con “XXXX”.'
        prompt += 'En las lineas de más abajo tienes el json con las facturas impagadas:'
        prompt += str(data.dict())
        response = model.generate_content([prompt])
        with open(Log_file, "a") as f:
            f.write(f"Prompt sent to Gemini API: {prompt}\n")
        text = response.text.replace("\"", "")
        with open(Log_file, "a") as f:
            f.write(f"Response from Gemini API: {response}\n")
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return JSONResponse(status_code=400, content={"success": False, "error": f"Error from Gemini API: {str(e)}"})
    return JSONResponse(status_code=200, content={"success": True, "caption": text})
