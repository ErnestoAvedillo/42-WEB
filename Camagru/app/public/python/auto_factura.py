from dataclasses import dataclass
from fastapi import UploadFile, File
from fastapi.responses import JSONResponse
import google.generativeai as genai
import os
import time
import re
import json

Log_file = "/var/log/app.log"

async def autoFactura(factura: UploadFile = File(...)):
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
        prompt = 'Devuelve la siguiente información de este documento en un archivo JSON exacto, sin explicaciones ni texto adicional:'
        prompt += 'Las fechas las debes devolver en formato d/m/Y'
        prompt += '{"acreedor":{"nombre":"","CIF":"","domicilio":"","telefono":"","FAX":"","email":""}}'
        prompt += '{"deudor":{"nombre":"","CIF":"","domicilio":"","telefono":"","FAX":"","email":""}}'
        prompt += '{"numero_contrato":""}'
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
