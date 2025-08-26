<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/request_login/request_login.php');
  exit();
}
$autofilling = '/tmp/debug_demandas.log';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/demand/demand.css">

</head>

<body>
  <?php
  $pageTitle = "Start a demand";
  include __DIR__ . '/../header/header.php';
  include __DIR__ . '/../left_bar/left_bar.php';
  ?>
  <div class="Facturas" id="Facturas">
    <h1>Carge el documento con la factura escaneada o en PDF</h1>
    <form id="demandForm" action="/pages/demand/demand_handler.php" method="post" enctype="multipart/form-data">
      <input id="file_field" type="file" name="factura" accept=".jpg,.jpeg,.png,.gif,.pdf,.mp4,.zip,.docx" required>
      <input type="hidden" name="user_uuid" value="<?php echo htmlspecialchars(SessionManager::getSessionKey('uuid')); ?>">
      <button id="fill_demand" class="button_demand" type="submit">Rellenar demanda</button>
    </form>
    <p> Ficheros admitidos: JPG, PDF, DOCX</p>
  </div>

  <?php include __DIR__ . '/../../utils/wait/wait.php'; ?>
  <h2>Facturas</h2>
  <table id="facturasTable">
    <thead>
      <tr>
        <th colspan="3">Cabecera</th>
        <th colspan="6">Información del emisor</th>
        <th colspan="6">Información del receptor</th>
        <th colspan="6">Información de la factura</th>
      </tr>
      <tr>
        <th>Edit</th>
        <th>ID</th>
        <th>User UUID</th>
        <th>Nombre</th>
        <th>CIF</th>
        <th>Domicilio</th>
        <th>Telefono</th>
        <th>FAX</th>
        <th>Email</th>
        <th>Nombre</th>
        <th>CIF</th>
        <th>Domicilio</th>
        <th>Telefono</th>
        <th>FAX</th>
        <th>Email</th>
        <th>Numero</th>
        <th>Fecha</th>
        <th>Vencimiento</th>
        <th>Importe Total</th>
        <th>Importe IVA</th>
        <th>Importe Sin IVA</th>
        <th>Concepto</th>
      </tr>
    </thead>
    <tbody>
      <?php
      require_once __DIR__ . '/../../database/facturas.php';
      $facturasInstance = new Facturas();
      $_user_uuid = SessionManager::getSessionKey('uuid');
      $facturas = $facturasInstance->getAll($_user_uuid);
      file_put_contents($autofilling, "Factura: " . json_encode($facturas) . "\n");

      foreach ($facturas as $factura) {
        file_put_contents($autofilling, "Factura: " . json_encode($factura['id']) . "\n");
        foreach ($factura as $key => $value) {

          file_put_contents($autofilling, "Factura " . $key . ": " . json_encode($value) . "\n");
        }
      }
      foreach ($facturas as $factura) {
        //$factura = json_encode($factura);
        echo "<tr>";
        $link_edit = '/pages/demand/edit_factura/edit_factura.php?id=' . $factura['id'];
        file_put_contents($autofilling, "Edit link: " . $link_edit . "\n", FILE_APPEND);
        echo "<td><a class=edit_factura href='" . $link_edit . "'><img src='/img/icon_edit.png' alt='Edit' width='20' height='20'></a></td>";
        $link_remove = '/pages/demand/delete_factura/delete_factura.php?id=' . $factura['id'];
        file_put_contents($autofilling, "Remove link: " . $link_remove . "\n", FILE_APPEND);
        //echo "<td><a class=delete_factura href='" . $link_remove . "'><img src='/img/icon_delete.png' alt='Delete' width='20' height='20'></a></td>";
        echo "<td class=delete_factura data-id=" . $factura['id'] . "><img src='/img/icon_delete.png' alt='Delete' width='20' height='20'></td>";
        echo "<td>" . htmlspecialchars($factura['id']) . "</td>";
        echo "<td>" . htmlspecialchars($factura['user_uuid']) . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_nombre'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_cif'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_domicilio'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_telefono'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_FAX'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['acreedor_email'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_nombre'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_cif'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_domicilio'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_telefono'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_FAX'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['deudor_email'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_numero'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_fecha'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_vencimiento'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_importe_total'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_importe_iva'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['factura_importe_sin_iva'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($factura['concepto'] ?? '') . "</td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>

  <?php
  include __DIR__ . '/../right_bar/right_bar.php';
  include __DIR__ . '/../../views/footer.php';
  ?>
  <script src="/pages/demand/demand_fill.js"></script>
  <script src="/pages/demand/delete_factura/delete_factura.js"></script>
</body>

</html>