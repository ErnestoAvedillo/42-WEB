#! /usr/bin/env python

from fastapi import FastAPI, Request
from pydantic import BaseModel

app = FastAPI()

class InputData(BaseModel):
    picture: str
    
@app.post("/autofill")
async def autofill(data: InputData):
    if not data.picture:
        return {"success": False, "error": "No picture provided"}

    caption = f"Texto generado por Python para la imagen."
    return {"success": True, "caption": caption}