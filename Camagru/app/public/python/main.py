#! /usr/bin/env python
from data_classes import InputData, ConceptoData, Factura, Contacto, PicturesList
from urllib import response
from fastapi import FastAPI, Request, File, UploadFile
from fastapi.responses import JSONResponse
from auto_fill import autoFill
from auto_factura import autoFactura
from auto_concepto import autoConcepto
from magic import combine_images_with_gemini as magicCombine

from PIL import Image
import base64
import time
import json


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

@app.post("/magic_combine")
async def magic_combine(request: Request):
    try:
        with open(Log_file, "a") as f:
            f.write(f"Request emited {time.strftime('%Y-%m-%d %H:%M:%S')}- Entrando en magic_combine\n")
        data = await request.json()
        with open(Log_file, "a") as f:
            f.write(f"Request emited {time.strftime('%Y-%m-%d %H:%M:%S')}- {json.dumps(data)}\n")
        response = magicCombine(data)  # This now returns a dict
        with open(Log_file, "a") as f:
            f.write(f"Response recived {time.strftime('%Y-%m-%d %H:%M:%S')}- {json.dumps(response, default=str)}\n")
        
        # Return appropriate HTTP status based on success
        if response.get("success", False):
            return JSONResponse(status_code=200, content=response)
        else:
            return JSONResponse(status_code=400, content=response)
            
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error processing request: {str(e)}\n")
        return JSONResponse(status_code=400, content={"success": False, "error": str(e)})

@app.post("/factura")
async def factura(factura: UploadFile = File(...)):
    response = await autoFactura(factura)
    with open( Log_file,"a") as f:
        f.write(f"Response recived {time.strftime('%Y-%m-%d %H:%M:%S')}- {json.dumps(response, default=str)}\n")
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
    response = await autoConcepto(data)
    with open(Log_file, "a") as f:
        f.write(f"Response recived {time.strftime('%Y-%m-%d %H:%M:%S')}- {json.dumps(response, default=str)}\n")
    return response