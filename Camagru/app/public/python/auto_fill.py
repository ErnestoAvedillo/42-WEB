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


async def autoFill(data: InputData):
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
        # response = "GenerateContentResponse(\n"
        # response += "done=True,\n"
        # response += "iterator=None,\n"
        # response += "result=protos.GenerateContentResponse({\n"
        # response += "  \"candidates\": [\n"
        # response += "    {\n"
        # response += "      \"content\": {\n"
        # response += "        \"parts\": [\n"
        # response += "          {\n"
        # response += "            \"text\": \"Me and my co-pilot, Spongebob, ready for a cross-country road trip.  He's in charge of the Krabby Patties.\"\n"
        # response += "          }\n"
        # response += "        ]\n"
        # response += "      }\n"
        # response += "      \"role\": \"model\"\n"
        # response += "    },\n"
        # response += "    \"finish_reason\": \"STOP\",\n"
        # response += "    \"avg_logprobs\": -0.4304741450718471\n"
        # response += "  }\n"
        # response += "],\n"
        # response += "  \"usage_metadata\": {\n"
        # response += "    \"prompt_token_count\": 266,\n"
        # response += "    \"candidates_token_count\": 35,\n"
        # response += "    \"total_token_count\": 301\n"
        # response += "  },\n"
        # response += "  \"model_version\": \"gemini-1.5-flash-latest\"\n"
        # response += "})"
        with open(Log_file, "a") as f:
            f.write(f"Prompt sent to Gemini API: {prompt}\n")
            f.write(f"Response type {type(response)} from Gemini API: {response.text}\n")
        text = response.text.strip('"')  # Remove any surrounding quotes if present
        with open(Log_file, "a") as f:
            f.write(f"Response type {type(response)} from Gemini API: {text}\n")
        return {"success": True, "caption": text}
    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return {"success": False, "error": f"Error from Gemini API: {str(e)}"}
"""
if __name__ == "__main__":
    # Example usage
    with open(Log_file, "a") as f:
        f.write(f"Function called {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
    with open(file)
    sample_data = InputData(picture="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAYAAAA+5n4HAAAAOXRFWHRTb2Z0d2FyZQBNYXRwbG90bGliIHZlcnNpb24zLjguNCwgaHR0cHM6Ly9tYXRwbG90bGliLm9yZy8fJSN1AAAACXBIWXMAAA9hAAAPYQGoP6dpAAEAAElEQVR4nOzde5xU1f3/8df+9mZ2Z3Zmd2Z2Z2ZmdmZ2Z"""