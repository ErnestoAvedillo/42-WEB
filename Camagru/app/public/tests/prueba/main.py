from urllib import response
from fastapi.responses import JSONResponse
import google.generativeai as genai
import os
import time
import json
import re

try:
    GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
    genai.configure(api_key=GOOGLE_API_KEY)

    model = genai.GenerativeModel('models/gemini-1.5-flash-latest')
    prompt = 'Eres un asistente jurídico.'
    prompt += 'Redacta el un escrito de demanda de tipo de cantidad para presentar ante el Juzgado de Primera Instancia en España.'
    prompt += 'Debes retornar la informacion en un json con los siguientes apartados:'
    prompt += '{"juzgado":""}, indicando el juzgado competente según la cuantía de la deuda.'
    prompt += '{"datos_acreedor":""}, indicando los datos del acreedor (nombre, NIF, dirección).'
    prompt += '{"datos_deudor":""}, indicando los datos del deudor (nombre, NIF, dirección).'
    prompt += '{"origen_deuda":""}, describiendo la relación contractual y detallando cada factura (número, fecha, vencimiento, concepto, importe).'
    prompt += '{"documentos_adjuntos":""}, listando los documentos que se adjuntan a la demanda (facturas, contratos, comunicaciones).'
    prompt += '{"solicitud_medidas":""}, especificando las medidas cautelares solicitadas, si las hay.'
    prompt += 'Importante: redacta el texto en formato claro y profesional, conciso y directo, evitando ambigüedades listo para adaptar y presentar en el juzgado.'
    response = model.generate_content([prompt])
    print (response.text)
    clean_str = re.sub(r"^```json\s*|\s*```$", "", response.text, flags=re.DOTALL)
    json_data = json.loads(clean_str)
    for key, value in json_data.items():
        print(f"para el valor {key} ---> {value}\n")
        print("-------------------------------------------\n")
except Exception as e:
    print(f"Error from Gemini API: {str(e)}\n")




"""import os
import json
import re

with open("text2.txt", "r") as f:
text = f.read()

clean_str = re.sub(r"^```json\s*|\s*```$", "", text, flags=re.DOTALL)
json_data = json.loads(clean_str)
for key, value in json_data.items():
print(f"{key}: {value}",end="\n\n")"""