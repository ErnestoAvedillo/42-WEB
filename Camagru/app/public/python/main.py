#! /usr/bin/env python

from urllib import response
from fastapi import FastAPI, Request
from pydantic import BaseModel
from PIL import Image
import base64
import os
import io
import html
import json
import ast
import re


def fix_base64_padding(encoded_string):
    missing_padding = len(encoded_string) % 4
    if missing_padding:
        encoded_string += '=' * (4 - missing_padding)
    return encoded_string

app = FastAPI()

class InputData(BaseModel):
    picture: str
    
@app.post("/autofill")
async def autofill(data: InputData):
    import google.generativeai as genai
#    models = genai.list_models()
#    with open("/var/log/app.log", "a") as f:
#        for model in models:
#            f.write(f"Model: {model.name}, Supported Methods: {model.supported_generation_methods}\n")

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
        with open("/var/log/app.log", "a") as f:
            f.write(f"Error processing images: {str(e)}\n")
        return {"success": False, "error": f"Invalid image format or data: {str(e)}"}

    try:
        GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        genai.configure(api_key=GOOGLE_API_KEY)

        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')

        response = model.generate_content(["Return only one funny caption for this picture", image_json])
        text = response.text.replace("\"", "")
        return {"success": True, "caption": text}
    except Exception as e:
        with open("/var/log/app.log", "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return {"success": False, "error": f"Error from Gemini API: {str(e)}"}
