<?php

use Ramsey\Uuid\Type\Time;

require_once __DIR__ . '/../database/pg_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Facturas
{
  private $autofilling = '/tmp/facturas.log';
  private $pdo;
  public function __construct()
  {
    try {
      $pgDatabase = new PGDatabase();
      $this->pdo = $pgDatabase->getConnection();
      if (!$this->pdo) {
        throw new Exception("Failed to connect to the database.");
      }
    } catch (Exception $e) {
      error_log("Failed to connect to database: " . $e->getMessage());
      throw new Exception("Database connection error");
    }
  }

  public function addFactura($data)
  {
    try {
      $stmt = $this->pdo->prepare("
            INSERT INTO facturas (user_uuid,
            document_uuid,
            status,
            acreedor_nombre,
            acreedor_cif,
            acreedor_domicilio,
            acreedor_telefono,
            acreedor_fax,
            acreedor_email,
            deudor_nombre,
            deudor_cif,
            deudor_domicilio,
            deudor_telefono,
            deudor_fax,
            deudor_email,
            factura_numero,
            factura_fecha,
            factura_vencimiento,
            factura_importe_total,
            factura_importe_iva,
            factura_importe_base,
            concepto,
            created_at) 
            VALUES (
            :user_uuid,
            :document_uuid,
            :status,
            :acreedor_nombre,
            :acreedor_cif,
            :acreedor_domicilio,
            :acreedor_telefono,
            :acreedor_fax,
            :acreedor_email,
            :deudor_nombre,
            :deudor_cif,
            :deudor_domicilio,
            :deudor_telefono,
            :deudor_fax,
            :deudor_email,
            :factura_numero,
            :factura_fecha,
            :factura_vencimiento,
            :factura_importe_total,
            :factura_importe_iva,
            :factura_importe_base,
            :concepto,
            :created_at)");
      $stmt->bindParam(':user_uuid', $data['user_uuid']);
      $stmt->bindParam(':document_uuid', $data['document_uuid']);
      $status = $data['status'] ?? 'pending';
      $stmt->bindParam(':status', $status);
      $stmt->bindParam(':acreedor_nombre', $data['acreedor']['nombre']);
      $stmt->bindParam(':acreedor_cif', $data['acreedor']['CIF']);
      $stmt->bindParam(':acreedor_domicilio', $data['acreedor']['domicilio']);
      $stmt->bindParam(':acreedor_telefono', $data['acreedor']['telefono']);
      $stmt->bindParam(':acreedor_fax', $data['acreedor']['FAX']);
      $stmt->bindParam(':acreedor_email', $data['acreedor']['email']);
      $stmt->bindParam(':deudor_nombre', $data['deudor']['nombre']);
      $stmt->bindParam(':deudor_cif', $data['deudor']['CIF']);
      $stmt->bindParam(':deudor_domicilio', $data['deudor']['domicilio']);
      $stmt->bindParam(':deudor_telefono', $data['deudor']['telefono']);
      $stmt->bindParam(':deudor_fax', $data['deudor']['FAX']);
      $stmt->bindParam(':deudor_email', $data['deudor']['email']);
      $stmt->bindParam(':factura_numero', $data['factura']['numero']);
      $facturaFecha = convertstring2date($data['factura']['fecha'], 'Y/m/d', 'Y/m/d');
      $stmt->bindParam(':factura_fecha', $facturaFecha);
      $facturaVencimiento = convertstring2date($data['factura']['vencimiento'], 'Y/m/d', 'Y/m/d');
      $stmt->bindParam(':factura_vencimiento', $facturaVencimiento);
      $stmt->bindParam(':factura_importe_total', $data['factura']['importe_total']);
      $stmt->bindParam(':factura_importe_iva', $data['factura']['importe_iva']);
      $stmt->bindParam(':factura_importe_base', $data['factura']['importe_base']);
      $stmt->bindParam(':concepto', $data['concepto']);
      $stmt->bindParam(':created_at', date('Y-m-d H:i:s'));
      $stmt->execute();
    } catch (Exception $e) {
      file_put_contents($this->autofilling, "Facturas DB -" . date('Y-m-d H:i:s') . "-Failed to add factura: " . $e->getMessage() . " Datos: " . json_encode($data) . "\n", FILE_APPEND);
      error_log("Failed to add factura: " . $e->getMessage());
      throw new Exception("Failed to add factura");
    }
  }
  public function updateFactura($facturaId, $user_uuid, $data)
  {
    file_put_contents($this->autofilling, "Facturas DB -" . date('Y-m-d H:i:s') . "-SQL in function updateFactura\n", FILE_APPEND);
    try {
      file_put_contents($this->autofilling, "Facturas DB -" . date('Y-m-d H:i:s') . "-dentro del try\n", FILE_APPEND);
      $stmt = $this->pdo->prepare("UPDATE facturas SET
        acreedor_nombre = :acreedor_nombre,
        acreedor_cif = :acreedor_cif,
        acreedor_domicilio = :acreedor_domicilio,
        acreedor_telefono = :acreedor_telefono,
        acreedor_fax = :acreedor_fax,
        acreedor_email = :acreedor_email,
        deudor_nombre = :deudor_nombre,
        deudor_cif = :deudor_cif,
        deudor_domicilio = :deudor_domicilio,
        deudor_telefono = :deudor_telefono,
        deudor_fax = :deudor_fax,
        deudor_email = :deudor_email,
        factura_numero = :factura_numero,
        factura_fecha = :factura_fecha,
        factura_vencimiento = :factura_vencimiento,
        factura_importe_total = :factura_importe_total,
        factura_importe_iva = :factura_importe_iva,
        factura_importe_base = :factura_importe_base,
        concepto = :concepto,
        updated_at = :updated_at
        WHERE id = :id AND user_uuid = :user_uuid");

      $updatedAt = date('Y-m-d H:i:s');
      $stmt->bindParam(':id', $facturaId);
      $stmt->bindParam(':user_uuid', $user_uuid);
      $stmt->bindParam(':acreedor_nombre', $data['acreedor_nombre']);
      $stmt->bindParam(':acreedor_cif', $data['acreedor_cif']);
      $stmt->bindParam(':acreedor_domicilio', $data['acreedor_domicilio']);
      $stmt->bindParam(':acreedor_telefono', $data['acreedor_telefono']);
      $stmt->bindParam(':acreedor_fax', $data['acreedor_fax']);
      $stmt->bindParam(':acreedor_email', $data['acreedor_email']);
      $stmt->bindParam(':deudor_nombre', $data['deudor_nombre']);
      $stmt->bindParam(':deudor_cif', $data['deudor_cif']);
      $stmt->bindParam(':deudor_domicilio', $data['deudor_domicilio']);
      $stmt->bindParam(':deudor_telefono', $data['deudor_telefono']);
      $stmt->bindParam(':deudor_fax', $data['deudor_fax']);
      $stmt->bindParam(':deudor_email', $data['deudor_email']);
      $stmt->bindParam(':factura_numero', $data['factura_numero']);
      $stmt->bindParam(':factura_fecha', $data['factura_fecha']);
      $stmt->bindParam(':factura_vencimiento', $data['factura_vencimiento']);
      $stmt->bindParam(':factura_importe_total', $data['factura_importe_total']);
      $stmt->bindParam(':factura_importe_iva', $data['factura_importe_iva']);
      $stmt->bindParam(':factura_importe_base', $data['factura_importe_base']);
      $stmt->bindParam(':concepto', $data['concepto']);
      $stmt->bindParam(':updated_at', $updatedAt);
      $stmt->execute();
      return true;
    } catch (Exception $e) {
      file_put_contents($this->autofilling, "Facturas DB -" . date('Y-m-d H:i:s') . "-Failed to update factura: " . $e->getMessage() . " Datos: " . json_encode($data) . "\n", FILE_APPEND);
      error_log("Failed to update factura: " . $e->getMessage());
      throw new Exception("Failed to update factura");
    }
  }
  public function delete($id)
  {
    try {
      $stmt = $this->pdo->prepare("DELETE FROM facturas WHERE id = :id");
      $stmt->bindParam(':id', $id);
      $stmt->execute();
    } catch (Exception $e) {
      error_log("Failed to delete factura: " . $e->getMessage());
      throw new Exception("Failed to delete factura");
    }
  }
  public function getDocumentoByFacturaId($facturaId)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT document_uuid FROM facturas WHERE id = :id");
      $stmt->bindParam(':id', $facturaId);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (Exception $e) {
      error_log("Failed to get document by factura ID: " . $e->getMessage());
      throw new Exception("Failed to get document by factura ID");
    }
  }
  public function getFactura($uuid_document)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT * FROM facturas WHERE document_uuid = :document_uuid");
      $stmt->bindParam(':document_uuid', $uuid_document);
      $stmt->execute();
      return $stmt->fetchAll();
    } catch (Exception $e) {
      error_log("Failed to get factura: " . $e->getMessage());
      throw new Exception("Failed to get factura");
    }
  }

  public function getFacturaById($id)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT * FROM facturas WHERE id = :id");
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      return $stmt->fetch();
    } catch (Exception $e) {
      error_log("Failed to get factura: " . $e->getMessage());
      throw new Exception("Failed to get factura");
    }
  }

  public function updateStatus($id, $newStatus)
  {
    try {
      $stmt = $this->pdo->prepare("UPDATE facturas SET status = :status WHERE id = :id");
      $stmt->bindParam(':status', $newStatus);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
    } catch (Exception $e) {
      error_log("Failed to change status: " . $e->getMessage());
      throw new Exception("Failed to change status");
    }
  }

  public function getStatus($uuid_document)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT status FROM facturas WHERE document_uuid = :document_uuid");
      $stmt->bindParam(':document_uuid', $uuid_document);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (Exception $e) {
      error_log("Failed to get status: " . $e->getMessage());
      throw new Exception("Failed to get status");
    }
  }
  public function getAll($user_uuid = null, $status = null)
  {
    try {
      if ($status) {
        $stmt = $this->pdo->prepare("SELECT * FROM facturas WHERE user_uuid = :user_uuid AND status = :status");
        $stmt->bindParam(':status', $status);
      } else {
        $stmt = $this->pdo->prepare("SELECT * FROM facturas WHERE user_uuid = :user_uuid");
      }
      if ($user_uuid) {
        $stmt->bindParam(':user_uuid', $user_uuid);
      } else {
        $stmt->bindValue(':user_uuid', null, PDO::PARAM_NULL);
      }
      $stmt->execute();
      return $stmt->fetchAll();
    } catch (Exception $e) {
      error_log("Failed to get all facturas: " . $e->getMessage());
      throw new Exception("Failed to get all facturas");
    }
  }

  public function filterFacturas($uuid, $acreedor, $deudor, $status)
  {
    try {
      $query = "SELECT * FROM facturas WHERE 1=1";
      $params = [];

      if (!empty($uuid)) {
        $query .= " AND document_uuid = :document_uuid";
        $params[':document_uuid'] = $uuid;
      }

      if (!empty($acreedor)) {
        $query .= " AND acreedor_nombre ILIKE :acreedor_nombre";
        $params[':acreedor_nombre'] = "%" . $acreedor . "%";
      }

      if (!empty($deudor)) {
        $query .= " AND deudor_nombre ILIKE :deudor_nombre";
        $params[':deudor_nombre'] = "%" . $deudor . "%";
      }

      if (!empty($status)) {
        $query .= " AND status = :status";
        $params[':status'] = $status;
      }

      $stmt = $this->pdo->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll();
    } catch (Exception $e) {
      error_log("Failed to filter facturas: " . $e->getMessage());
      throw new Exception("Failed to filter facturas");
    }
  }

  // Add your methods for handling "facturas" here
}
