from pdf2image import convert_from_path
import pytesseract

def ocr_pdf(path):
    pages = convert_from_path(path)  # convierte cada página en un objeto PIL.Image
    text = ""
    for page in pages:
        text += pytesseract.image_to_string(page, lang="spa") + "\n"
    return text

