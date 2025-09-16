#! /usr/bin/env python
from data_classes import InputData, ConceptoData, Factura, Contacto
from urllib import response
from fastapi import FastAPI, Request, File, UploadFile
from fastapi.responses import JSONResponse
from auto_fill import autoFill
from auto_factura import autoFactura

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

@app.post("/autofill")
async def autofill(data: InputData):
    response = await autoFill(data)
    return response


@app.post("/factura")
async def factura(factura: UploadFile = File(...)):
    response = await autoFactura(factura)
    with open( Log_file,"a") as f:
        f.write(f"Response recived {time.strftime('%Y-%m-%d %H:%M:%S')}- {response}\n")
    return response

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
