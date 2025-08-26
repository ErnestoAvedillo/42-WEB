<div id="factura">
  <div id="Acreedor">
    <form id="acreedorForm">
      <h2>Acreedor</h2>
      <label for="emisor_nombre">Nombre:</label>
      <input type="text" id="emisor_nombre" name="emisor_nombre"><br>

      <label for="emisor_cif">CIF:</label>
      <input type="text" id="emisor_cif" name="emisor_cif"><br>

      <label for="emisor_domicilio">Domicilio:</label>
      <input type="text" id="emisor_domicilio" name="emisor_domicilio"><br>

      <label for="emisor_telefono">Teléfono:</label>
      <input type="text" id="emisor_telefono" name="emisor_telefono"><br>

      <label for="emisor_fax">FAX:</label>
      <input type="text" id="emisor_FAX" name="emisor_FAX"><br>

      <label for="emisor_email">Email:</label>
      <input type="email" id="emisor_email" name="emisor_email"><br>
    </form>
  </div>
  <div id="Deudor">
    <form id="deudorForm">
      <h2>Deudor</h2>
      <label for="receptor_nombre">Nombre:</label>
      <input type="text" id="receptor_nombre" name="receptor_nombre"><br>

      <label for="receptor_cif">CIF:</label>
      <input type="text" id="receptor_cif" name="receptor_cif"><br>

      <label for="receptor_domicilio">Domicilio:</label>
      <input type="text" id="receptor_domicilio" name="receptor_domicilio"><br>

      <label for="receptor_telefono">Teléfono:</label>
      <input type="text" id="receptor_telefono" name="receptor_telefono"><br>

      <label for="receptor_FAX">FAX:</label>
      <input type="text" id="receptor_FAX" name="receptor_FAX"><br>

      <label for="receptor_email">Email:</label>
      <input type="email" id="receptor_email" name="receptor_email"><br>
    </form>
  </div>
  <div id="Factura">
    <form id="facturaForm">
      <h2>Factura</h2>
      <label for="factura_numero">Número:</label>
      <input type="text" id="factura_numero" name="factura_numero"><br>

      <label for="factura_fecha">Fecha:</label>
      <input type="text" id="factura_fecha" name="factura_fecha"><br>

      <label for="factura_vencimiento">Vencimiento:</label>
      <input type="text" id="factura_vencimiento" name="factura_vencimiento"><br>

      <label for="factura_importe_total">Importe Total:</label>
      <input type="text" id="factura_importe_total" name="factura_importe_total"><br>

      <label for="factura_importe_iva">Importe IVA:</label>
      <input type="text" id="factura_importe_iva" name="factura_importe_iva"><br>

      <label for="factura_importe_sin_iva">Importe sin IVA:</label>
      <input type="text" id="factura_importe_sin_iva" name="factura_importe_sin_iva"><br>
    </form>
  </div>
  <div>
    <form>
      <h2>Concepto</h2>
      <label for="concepto">Descripción:</label>
      <textarea id="concepto" name="concepto" rows="4"></textarea><br>
    </form>
  </div>
</div>