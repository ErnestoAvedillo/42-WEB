from typing import List,Optional
from pydantic import BaseModel
from fastapi import UploadFile, File

class InputData(BaseModel):
    picture: Optional[str] = None
    factura: Optional[str] = None

class Factura(BaseModel):
    fecha: Optional[str] = None
    importe: str
    concepto: str
    vencimiento: Optional[str] = None
    document_uuid: str

class Contacto(BaseModel):
    nombre: str
    cif: str
    domicilio: str
    telefono: str
    fax: str
    email: str

class ConceptoData(BaseModel):
    acreedor: Contacto
    deudor: Contacto
    lista_facturas: dict[str, Factura]

class Pictures(BaseModel):
    img:  UploadFile = File(...)

class PicturesList(BaseModel):
    images: List[Pictures]