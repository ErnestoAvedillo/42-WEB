from pdfminer.high_level import extract_text

def is_image_pdf(path):
    text = extract_text(path)
    return not bool(text.strip())