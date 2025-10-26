"""combine images using Gemini API"""
import google.generativeai as genai

import os

from PIL import Image

import time

Log_file = "/var/log/app.log"


def combine_images_with_gemini(DataReceived):
    """
    Sends multiple images and a text prompt to the Gemini API to generate a new,
    combined image.
    Now accepts JSON data with base64 images instead of UploadFile objects.
    """
    # Expecting a dict with 'images' key
    image_data_list = DataReceived.get('images', [])
    try:
        image_json = []
        # image_data_list is now a list from the 'images' key in the JSON
        for image_data in image_data_list:
            # Extract base64 data from the img field
            img_data = image_data.get('img', '')

            # Handle data:image/type;base64,data format
            if img_data.startswith('data:'):
                # Split the data URL to get mime type and base64 data
                header, base64_data = img_data.split(',', 1)
                mime_type = header.split(':')[1].split(';')[0]
            else:
                # Assume it's just base64 data
                base64_data = img_data
                mime_type = "image/png"  # Default

            # Decode base64 to bytes
            import base64
            image_bytes = base64.b64decode(base64_data)

            image_json.append({"mime_type": mime_type, "data": image_bytes})

    except Exception as e:
        with open(Log_file, "a") as f:
            f.write(f"Error procesando imagen: {str(e)}\n")
        return {"success": False,
                "error": f"Invalid image format or data: {str(e)}"}
    with open(Log_file, "a") as f:
        f.write(f"Function called {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
    try:
        GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
        USE_MODEL = os.getenv("IMAGE_MODEL")
        # Initialize the client (it automatically looks for the GEMINI_API_KEY env var)
        genai.configure(api_key=GOOGLE_API_KEY)
        model = genai.GenerativeModel(USE_MODEL)
        prompt = "Create a cute cartoon-style 3D illustration "
        prompt += "Pixar style using both images. "
        prompt += "Make it artistic, fun, and clearly fictional and not harmful. "
        prompt += "Output should be a PNG image."
        prompt += f"The user request is: {DataReceived.get('prompt', '')}. "
        # 1. Load the images and prepare them for Gemini
        contents = [prompt]
        for image in image_json:
            # Create PIL Image from bytes
            from io import BytesIO
            img = Image.open(BytesIO(image["data"]))

            # Convert PIL image back to bytes for Gemini, ensuring PNG format
            img_byte_arr = BytesIO()
            img.save(img_byte_arr, format='PNG')
            img_bytes = img_byte_arr.getvalue()

            # The API expects a dictionary with mime_type and the raw bytes.
            contents.append({
                "mime_type": "image/png",
                "data": img_bytes
            })
        with open(Log_file, "a") as f:
            for item in contents:
                f.write(f"Gemini INPUT DATA ITEM: {str(item)[:100]}...\n")
            f.write(f"Gemini model used: {USE_MODEL}\n")
        """
        # Alternative: Use PIL to combine images directly
        if len(image_json) >= 2:
            try:
                from io import BytesIO

                # Load the two images
                img1 = Image.open(BytesIO(image_json[0]["data"]))
                img2 = Image.open(BytesIO(image_json[1]["data"]))

                # Create a combined image (simple overlay approach)
                # Resize img2 to fit img1 if needed
                base_width, base_height = img1.size

                # Resize img2 to be at most 50% of img1's size
                max_overlay_width = base_width // 2
                max_overlay_height = base_height // 2

                img2.thumbnail((max_overlay_width, max_overlay_height),
                               Image.Resampling.LANCZOS)

                # Create a copy of img1 to work with
                combined = img1.copy()

                # Position img2 in the center-right of img1
                overlay_x = base_width - img2.size[0] - 20
                overlay_y = (base_height - img2.size[1]) // 2

                # Paste img2 onto img1
                if img2.mode == 'RGBA':
                    combined.paste(img2, (overlay_x, overlay_y), img2)
                else:
                    combined.paste(img2, (overlay_x, overlay_y))

                # Convert to base64
                output_buffer = BytesIO()
                combined.save(output_buffer, format='PNG')
                combined_bytes = output_buffer.getvalue()

                import base64
                combined_b64 = base64.b64encode(combined_bytes).decode('utf-8')
                combined_data_url = f"data:image/png;base64,{combined_b64}"

                with open(Log_file, "a") as f:
                    f.write("✅ Success! Images combined using PIL.\n")

                return {
                    "success": True,
                    "image": combined_data_url,
                    "message": "Images combined successfully using PIL overlay"
                }

            except Exception as e:
                with open(Log_file, "a") as f:
                    f.write(f"Error combining images with PIL: {str(e)}\n")
                return {"success": False, "error": f"Error combining images: {str(e)}"}
            """
        # 3. Call the API to generate content (text description first)
        response = model.generate_content(contents=contents)
        # Revisar si la API devolvió imágenes o texto
        output_images = []
        output_texts = []
        for part in response.candidates[0].content.parts:
            if part.text:
                output_texts.append(part.text)
            elif part.inline_data:
                img = Image.open(BytesIO(part.inline_data.data))
                img_byte_arr = BytesIO()
                img.save(img_byte_arr, format="PNG")
                img_b64 = base64.b64encode(img_byte_arr.getvalue()).decode("utf-8")
                output_images.append(f"data:image/png;base64,{img_b64}")
        returned_text = ""
        for text in output_texts:
            returned_text += text + "\n"
        with open(Log_file, "a") as f:
            f.write(f"Gemini response: {returned_text}\n")
        # For now, return a simple success message with the text description
        # In the future, you might want to use another service to actually
        # generate/combine images

        return {
            "success": True,
            "message": returned_text if output_texts else "Combination successful",
            "images": output_images if output_images else None
        }
    except Exception as e:
        with open("/var/log/app.log", "a") as f:
            f.write(f"Error from Gemini API: {str(e)}\n")
        return {"success": False, "error": f"Error from Gemini API: {str(e)}"}
