from data_classes import ConceptoData
from urllib import response
from fastapi.responses import JSONResponse
import google.generativeai as genai
import os
import time
import json
import re

Log_file = "/var/log/app.log"

async def autoConcepto(data: ConceptoData):
    if Log_file and os.path.exists(Log_file):
        os.remove(Log_file)
    with open(Log_file, "a") as f:
        f.write(f"Function called {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
    try:
        # GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        GOOGLE_API_KEY = "AIzaSyBsudAu-dnMM847uCkeEhi7OCb69C3bSQ8"
        genai.configure(api_key=GOOGLE_API_KEY)

        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')
        prompt = 'Eres un asistente jurídico. Te paso un JSON con facturas impagadas (cada objeto contiene: número de factura, fecha de emisión, fecha de vencimiento, concepto).'
        prompt += 'Con esa información, redacta el un escrito de demanda de reclamación de cantidad en España para presentar ante el Juzgado de Primera Instancia.'
        prompt += 'Debes retornara la informacion en un json con los siguientes apartados:'
        prompt += '{"juzgado":""}, indicando el juzgado competente según la cuantía de la deuda.'
        prompt += '{"origen_deuda":""}, describiendo la relación contractual y detallando cada factura (número, fecha, vencimiento, concepto, importe).'
        prompt += '{"documentos_adjuntos":""}, listando los documentos que se adjuntan a la demanda (facturas, contratos, comunicaciones).'
        prompt += '{"solicitud_medidas":""}, especificando las medidas cautelares solicitadas, si las hay.'
        prompt += 'Importante: redacta el texto en formato claro y profesional, conciso y directo, evitando ambigüedades listo para adaptar y presentar en el juzgado.'
        prompt += 'No inventes datos de las partes; deja campos genéricos con “XXXX”.'
        prompt += 'En las lineas de más abajo tienes el json con las facturas impagadas y el json correspondiente a los acreedores y deudores:'
        prompt += str(data.model_dump())
        response = model.generate_content([prompt])
        with open(Log_file, "a") as f:
            f.write(f"Prompt sent to Gemini API: {prompt}\n")
        #text = response.text.replace("\"", "")
        with open(Log_file, "a") as f:
            f.write(f"Response from Gemini API: {response}\n")
        with open(Log_file, "a") as f:
            f.write(f"Response from Gemini API: {response.text}\n")
        clean_str = re.sub(r"^```json\s*|\s*```$", "", response.text, flags=re.DOTALL)
        json_data = json.loads(clean_str)
        for key, value in json_data.items():
            with open(Log_file, "a") as f:
                f.write(f"para el valor {key} ---> {value}\n")
                f.write("-------------------------------------------\n")
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return JSONResponse(status_code=400, content={"success": False, "error": f"Error from Gemini API: {str(e)}"})
    return JSONResponse(status_code=200, content={"success": True, "caption": json_data})
