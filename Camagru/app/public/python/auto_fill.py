#! /usr/bin/env python
from data_classes import InputData
from urllib import response
import google.generativeai as genai

from PIL import Image
import base64
import os
import io

Log_file = "/var/log/app.log"

def fix_base64_padding(encoded_string):
    missing_padding = len(encoded_string) % 4
    if missing_padding:
        encoded_string += '=' * (4 - missing_padding)
    return encoded_string


def autoFill(data: InputData):
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
