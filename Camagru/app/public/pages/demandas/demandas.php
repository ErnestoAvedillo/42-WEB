<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/request_login/request_login.php');
  exit();
}
$autofilling = '/tmp/demandas.log';
if (file_exists($autofilling)) {
  unlink($autofilling);
  file_put_contents($autofilling, "Demandas log created at " . date('Y-m-d H:i:s') . "\n");
}
$_user_uuid = SessionManager::getSessionKey('uuid');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/demandas/demandas.css">

</head>

<body>
  <?php
  $pageTitle = "Lista de demandas";
  include __DIR__ . '/../header/header.php';
  include __DIR__ . '/../left_bar/left_bar.php';
  ?>
  <?php include __DIR__ . '/../../utils/wait/wait.php'; ?>
  <div class="demandasDiv">
    <h2>Demandas</h2>
    <table id="demandasTable">
      <thead>
        <tr>
          <th colspan="3">Cabecera</th>
          <th></th>
          <th colspan="6">Información del emisor</th>
          <th colspan="6">Información del receptor</th>
        </tr>
        <tr>
          <th colspan="2">Manage</th>
          <th>ID</th>
          <th>Status</th>
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
        </tr>
      </thead>
      <tbody>
        <?php
        require_once __DIR__ . '/../../database/demandas.php';
        $demandasInstance = new Demandas();
        $demandas = $demandasInstance->getAll($_user_uuid);
        file_put_contents($autofilling, "Demandas:" . date('Y-m-d H:i:s') . "-Demanda: " . json_encode($demandas) . "\n");

        foreach ($demandas as $demanda) {
          file_put_contents($autofilling, "Demandas:" . date('Y-m-d H:i:s') . "-Demanda: " . json_encode($demanda['id']) . "\n");
          foreach ($demanda as $key => $value) {

            file_put_contents($autofilling, "Demandas:" . date('Y-m-d H:i:s') . "-Demanda " . $key . ": " . json_encode($value) . "\n");
          }
        }
        foreach ($demandas as $demanda) {
          //$demanda = json_encode($demanda);
          echo "<tr>";
          $link_edit = '/pages/demandas/edit_demanda/edit_demanda.php?id=' . $demanda['id'];
          file_put_contents($autofilling, "Demandas:" . date('Y-m-d H:i:s') . "Edit link: " . $link_edit . "\n", FILE_APPEND);
          echo "<td class=delete_demanda data-id=" . $demanda['id'] . "><img src='/img/icon_delete.png' alt='Delete' width='20' height='20'></td>";
          echo "<td class=edit_demanda data-id='" . $demanda['id'] . "'><img src='/img/icon_edit.png' alt='Edit' width='20' height='20'></td>";
          echo "<td>" . htmlspecialchars($demanda['id']) . "</td>";
          echo "<td>" . htmlspecialchars($demanda['status']) . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_nombre'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_cif'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_domicilio'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_telefono'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_fax'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['acreedor_email'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_nombre'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_cif'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_domicilio'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_telefono'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_fax'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($demanda['deudor_email'] ?? '') . "</td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Modal container -->
  <div id="editDemandaModal" class="modal">
    <div class="modal-content">
      <span class="close-button">&times;</span>
      <div id="modalBody"></div>
    </div>
  </div>




  <?php
  include __DIR__ . '/../right_bar/right_bar.php';
  include __DIR__ . '/../../views/footer.php';
  ?>
  <script src="/pages/demandas/demandas.js"></script>
  <script src="/pages/demandas/delete_demanda/delete_demanda.js"></script>
  <script src="/pages/demandas/edit_demanda/edit_demanda.js"></script>
</body>

</html>