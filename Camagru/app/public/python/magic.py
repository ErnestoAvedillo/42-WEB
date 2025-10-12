from google import genai
from google.genai import types
from PIL import Image
from io import BytesIO

# --- Configuration ---
# You'll need an image-enabled model for this task
MODEL_NAME = 'gemini-2.5-flash-image-preview' # Or the latest image-capable model
OUTPUT_FILENAME = "combined_image.png"

# Replace with the actual paths to your two input pictures
IMAGE_PATH_1 = "path/to/your/first_picture.jpg"
IMAGE_PATH_2 = "path/to/your/second_picture.png"

# The descriptive prompt is CRITICAL for telling the model how to combine them
PROMPT = (
    "Combine the two pictures. Take the **subject** (a dog wearing a hat) "
    "from the first image and place it seamlessly into the **background** "
    "(a park at sunset) from the second image. The final result should be a "
    "single, photorealistic image."
)
# ---------------------

def combine_images_with_gemini(model_name: str, prompt: str, image_paths: list[str], output_file: str):
    """
    Sends multiple images and a text prompt to the Gemini API to generate a new, combined image.
    """
    try:
        # Initialize the client (it automatically looks for the GEMINI_API_KEY env var)
        client = genai.Client()

        # 1. Load the images
        image_parts = []
        for path in image_paths:
            img = Image.open(path)
            # Create a Part object from the PIL image
            image_part = types.Part.from_image(img)
            image_parts.append(image_part)
        
        # 2. Combine all parts into the content list: Image 1, Image 2, then the Prompt
        contents = image_parts + [prompt]

        print(f"Sending request to model: {model_name}...")
        
        # 3. Call the API to generate content (the combined image)
        # Note: Set response_mime_types to 'image/png' (or 'image/jpeg') to get image output
        response = client.models.generate_content(
            model=model_name,
            contents=contents,
            config=types.GenerateContentConfig(
                response_mime_types=["image/png"] # Specify that you want an image back
            )
        )

        # 4. Extract and save the generated image
        if response.generations and response.generations[0].image:
            # The generated image is in the 'image' field of the first generation
            generated_image_bytes = response.generations[0].image.image_bytes
            
            # Save the raw bytes as a file
            with open(output_file, 'wb') as f:
                f.write(generated_image_bytes)

            print(f"✅ Success! Combined image saved to {output_file}")
        else:
            print("❌ Failure: Could not find a generated image in the response.")
            # Print the text response for potential error messages or explanations
            print("Model Text Response:", response.text)

    except Exception as e:
        print(f"An error occurred: {e}")

# Execute the function
combine_images_with_gemini(MODEL_NAME, PROMPT, [IMAGE_PATH_1, IMAGE_PATH_2], OUTPUT_FILENAME)