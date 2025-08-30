<link href="/pages/facturas/edit_factura/edit_factura.css" rel="stylesheet">
<?php
require_once __DIR__ . '/../../../database/facturas.php';
$autofilling = '/tmp/debug_edit_factura.log';
if (file_exists($autofilling)) {
  unlink($autofilling);
}
file_put_contents($autofilling, "success receiving factura data" . time() . "\n", FILE_APPEND);
file_put_contents($autofilling, "Enter in edit_factura" . time() . "\n", FILE_APPEND);
$facturaId = $_GET['id'] ?? null;
if (!$facturaId) {
  echo json_encode(["error" => "Invalid factura ID.", "redirect" => "/pages/facturas/factura.php"]);
  exit();
}
$facturasInstance = new Facturas();
$factura = $facturasInstance->getFacturaById($facturaId);
if (!$factura) {
  echo json_encode(["error" => "Factura not found.", "redirect" => "/pages/facturas/factura.php"]);
  exit();
}

// Ensure all keys in $factura are strings and not null
foreach ($factura as $key => $value) {
  $factura[$key] = is_string($value) ? $value : '';
}

file_put_contents($autofilling, "success receiving factura data" . json_encode($factura) . "\n", FILE_APPEND);
?>
<div id="factura">
  <form id="formularioFactura" action="/pages/facturas/edit_factura/edit_factura_handler.php" method="POST">
    <div id="Acreedor">
      <h2>Acreedor</h2>
      <label for="acreedor_nombre">Nombre:</label>
      <input type="text" id="acreedor_nombre" name="acreedor_nombre" value="<?php echo htmlspecialchars($factura['acreedor_nombre']); ?>"><br>

      <label for="acreedor_cif">CIF:</label>
      <input type="text" id="acreedor_cif" name="acreedor_cif" value="<?php echo htmlspecialchars($factura['acreedor_cif']); ?>"><br>

      <label for="acreedor_domicilio">Domicilio:</label>
      <input type="text" id="acreedor_domicilio" name="acreedor_domicilio" value="<?php echo htmlspecialchars($factura['acreedor_domicilio']); ?>"><br>

      <label for="acreedor_telefono">Teléfono:</label>
      <input type="text" id="acreedor_telefono" name="acreedor_telefono" value="<?php echo htmlspecialchars($factura['acreedor_telefono']); ?>"><br>

      <label for="acreedor_fax">FAX:</label>
      <input type="text" id="acreedor_fax" name="acreedor_fax" value="<?php echo htmlspecialchars($factura['acreedor_fax']); ?>"><br>

      <label for="acreedor_email">Email:</label>
      <input type="email" id="acreedor_email" name="acreedor_email" value="<?php echo htmlspecialchars($factura['acreedor_email']); ?>"><br>
    </div>
    <div id="Deudor">
      <h2>Deudor</h2>
      <label for="deudor_nombre">Nombre:</label>
      <input type="text" id="deudor_nombre" name="deudor_nombre" value="<?php echo htmlspecialchars($factura['deudor_nombre']); ?>"><br>

      <label for="deudor_cif">CIF:</label>
      <input type="text" id="deudor_cif" name="deudor_cif" value="<?php echo htmlspecialchars($factura['deudor_cif']); ?>"><br>

      <label for="deudor_domicilio">Domicilio:</label>
      <input type="text" id="deudor_domicilio" name="deudor_domicilio" value="<?php echo htmlspecialchars($factura['deudor_domicilio']); ?>"><br>

      <label for="deudor_telefono">Teléfono:</label>
      <input type="text" id="deudor_telefono" name="deudor_telefono" value="<?php echo htmlspecialchars($factura['deudor_telefono']); ?>"><br>

      <label for="deudor_fax">FAX:</label>
      <input type="text" id="deudor_fax" name="deudor_fax" value="<?php echo htmlspecialchars($factura['deudor_fax']); ?>"><br>

      <label for="deudor_email">Email:</label>
      <input type="email" id="deudor_email" name="deudor_email" value="<?php echo htmlspecialchars($factura['deudor_email']); ?>"><br>
    </div>
    <div id="Factura">
      <h2>Factura</h2>
      <label for="factura_numero">Número:</label>
      <input type="text" id="factura_numero" name="factura_numero" value="<?php echo htmlspecialchars($factura['factura_numero']); ?>"><br>

      <label for="factura_fecha">Fecha:</label>
      <input type="text" id="factura_fecha" name="factura_fecha" value="<?php echo htmlspecialchars($factura['factura_fecha']); ?>"><br>

      <label for="factura_vencimiento">Vencimiento:</label>
      <input type="text" id="factura_vencimiento" name="factura_vencimiento" value="<?php echo htmlspecialchars($factura['factura_vencimiento']); ?>"><br>

      <label for="factura_importe_total">Importe Total:</label>
      <input type="text" id="factura_importe_total" name="factura_importe_total" value="<?php echo htmlspecialchars($factura['factura_importe_total']); ?>"><br>

      <label for="factura_importe_iva">Importe IVA:</label>
      <input type="text" id="factura_importe_iva" name="factura_importe_iva" value="<?php echo htmlspecialchars($factura['factura_importe_iva']); ?>"><br>

      <label for="factura_importe_sin_iva">Importe sin IVA:</label>
      <input type="text" id="factura_importe_base" name="factura_importe_base" value="<?php echo htmlspecialchars($factura['factura_importe_base']); ?>"><br>
    </div>
    <div id="Concepto">
      <h2>Concepto</h2>
      <label for="concepto">Descripción:</label>
      <textarea id="concepto" name="concepto" rows="4"><?php echo htmlspecialchars($factura['concepto']); ?></textarea><br>
    </div>
    <input type="hidden" name="factura_id" value="<?php echo htmlspecialchars($facturaId); ?>">
    <button type="submit" id="saveFacturaButton" data-id="<?php echo htmlspecialchars($facturaId); ?>">Save Changes</button>
    <button type="button" id="cancelEditButton">Cancel</button>
  </form>
</div>
</div>
<script src="/pages/facturas/edit_factura/handle_buttons_form.js"></script>