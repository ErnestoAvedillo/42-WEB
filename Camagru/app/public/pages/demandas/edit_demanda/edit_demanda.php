<link href="/pages/demandas/edit_demanda/edit_demanda.css" rel="stylesheet">
<?php
require_once __DIR__ . '/../../../database/demandas.php';
$autofilling = '/tmp/demandas.log';

file_put_contents($autofilling, "Editdemanda: " . date('Y-m-d H:i:s') . " Enter in edit_demanda" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
$demandaId = $_GET['id'] ?? null;
if (!$demandaId) {
  file_put_contents($autofilling, "Editdemanda: " . date('Y-m-d H:i:s') . " Invalid demanda ID." . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
  echo json_encode(["error" => "Invalid demanda ID.", "redirect" => "/pages/demandas/demanda.php"]);
  exit();
}
$demandasInstance = new Demandas();
$demanda = $demandasInstance->getDemandaById($demandaId);
if (!$demanda) {
  file_put_contents($autofilling, "Editdemanda: " . date('Y-m-d H:i:s') . " Demanda not found." . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
  echo json_encode(["error" => "Demanda not found.", "redirect" => "/pages/demandas/demanda.php"]);
  exit();
}
foreach ($demanda as $key => $value) {
  file_put_contents($autofilling, "Editdemanda: " . date('Y-m-d H:i:s') . " Demanda field: " . $key . " => " . $value . "\n", FILE_APPEND);
  $demanda[$key] = is_string($value) ? $value : '';
}
file_put_contents($autofilling, "Editdemanda: " . date('Y-m-d H:i:s') . " success receiving demanda data" . json_encode($demanda) . "\n", FILE_APPEND);
?>
<div id="demanda">
  <form id="formularioDemanda" action="/pages/demandas/edit_demanda/edit_demanda_handler.php" method="POST">
    <div id="DatosGenerales">
      <h2>Datos Generales</h2>
      <input type="hidden" id="user_uuid" name="user_uuid" value="<?php echo htmlspecialchars($demanda['user_uuid']); ?>">
      <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($demandaId); ?>">
      <div id="Acreedor">
        <h2>Datos del Acreedor</h2>
        <label for="acreedor_nombre">Nombre:</label>
        <input type="text" id="acreedor_nombre" name="acreedor_nombre" value="<?php echo htmlspecialchars($demanda['acreedor_nombre']); ?>"><br>
        <label for="acreedor_cif">CIF:</label>
        <input type="text" id="acreedor_cif" name="acreedor_cif" value="<?php echo htmlspecialchars($demanda['acreedor_cif']); ?>"><br>
        <label for="acreedor_domicilio">Domicilio:</label>
        <input type="text" id="acreedor_domicilio" name="acreedor_domicilio" value="<?php echo htmlspecialchars($demanda['acreedor_domicilio']); ?>"><br>
        <label for="acreedor_telefono">Teléfono:</label>
        <input type="text" id="acreedor_telefono" name="acreedor_telefono" value="<?php echo htmlspecialchars($demanda['acreedor_telefono']); ?>"><br>
        <label for="acreedor_fax">Fax:</label>
        <input type="text" id="acreedor_fax" name="acreedor_fax" value="<?php echo htmlspecialchars($demanda['acreedor_fax']); ?>"><br>
        <label for="acreedor_email">Email:</label>
        <input type="email" id="acreedor_email" name="acreedor_email" value="<?php echo htmlspecialchars($demanda['acreedor_email']); ?>"><br>
      </div>
      <div id="Deudor">
        <h2>Datos del Deudor</h2>
        <label for="deudor_nombre">Nombre:</label>
        <input type="text" id="deudor_nombre" name="deudor_nombre" value="<?php echo htmlspecialchars($demanda['deudor_nombre']); ?>"><br>
        <label for="deudor_cif">CIF:</label>
        <input type="text" id="deudor_cif" name="deudor_cif" value="<?php echo htmlspecialchars($demanda['deudor_cif']); ?>"><br>
        <label for="deudor_domicilio">Domicilio:</label>
        <input type="text" id="deudor_domicilio" name="deudor_domicilio" value="<?php echo htmlspecialchars($demanda['deudor_domicilio']); ?>"><br>
        <label for="deudor_telefono">Teléfono:</label>
        <input type="text" id="deudor_telefono" name="deudor_telefono" value="<?php echo htmlspecialchars($demanda['deudor_telefono']); ?>"><br>
        <label for="deudor_fax">Fax:</label>
        <input type="text" id="deudor_fax" name="deudor_fax" value="<?php echo htmlspecialchars($demanda['deudor_fax']); ?>"><br>
        <label for="deudor_email">Email:</label>
        <input type="email" id="deudor_email" name="deudor_email" value="<?php echo htmlspecialchars($demanda['deudor_email']); ?>"><br>
      </div>
      <label for="importe_total_deuda">Importe Total Deuda:</label>
      <input type="number" id="importe_total_deuda" name="importe_total_deuda" value="<?php echo htmlspecialchars($demanda['importe_total_deuda']); ?>"><br>
    </div>
    <div id="Concepto">
      <h2>Concepto</h2>
      <label for="concepto">Descripción:</label>
      <textarea id="concepto" name="concepto" rows=10><?php echo htmlspecialchars($demanda['concepto']); ?></textarea><br>
      <input type="hidden" id="lista_facturas" name="lista_facturas" value="<?php echo htmlspecialchars($demanda['lista_facturas']); ?>">
      <button type="button" id="genera_concepto" data-id="<?php echo htmlspecialchars($demanda['lista_facturas']); ?>">Genera Concepto</button>
    </div>
    <input type="hidden" name="demanda_id" value="<?php echo htmlspecialchars($demandaId); ?>">
    <button type="button" id="saveDemandaButton" data-id="<?php echo htmlspecialchars($demandaId); ?>">Save Changes</button>
    <button type="button" id="cancelEditButton">Cancel</button>
  </form>
</div>
</div>
<script src="/pages/demandas/edit_demanda/edit_demanda.js"></script>
<script src="/pages/demandas/edit_demanda/handle_buttons_form.js"></script>