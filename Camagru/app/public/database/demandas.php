<?php
require_once __DIR__ . '/../database/pg_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Demandas
{

  private $pdo;
  private $autofilling;
  public function __construct()
  {
    try {
      $pgDatabase = new PGDatabase();
      $this->pdo = $pgDatabase->getConnection();
      $this->autofilling = '/tmp/debug_demandas_db.log';
      if (file_exists($this->autofilling)) {
        unlink($this->autofilling);
      }
      file_put_contents($this->autofilling, "Entro en Demandas: \n", FILE_APPEND);
      if (!$this->pdo) {
        throw new Exception("Failed to connect to the database.");
      }
    } catch (Exception $e) {
      error_log("Failed to connect to database: " . $e->getMessage());
      throw new Exception("Database connection error");
    }
  }

  public function addDemand($demandData)
  {
    try {
      $stmt = $this->pdo->prepare("
            INSERT INTO demandas (user_uuid,
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
            importe_total_deuda,
            lista_facturas,
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
            :importe_total_deuda,
            :lista_facturas,
            :concepto,
            :created_at)");
      $stmt->bindParam(':user_uuid', $demandData['user_uuid']);
      $stmt->bindParam(':document_uuid', $demandData['document_uuid']);
      $stmt->bindParam(':status', $demandData['status']);
      $stmt->bindParam(':acreedor_nombre', $demandData['acreedor_nombre']);
      $stmt->bindParam(':acreedor_cif', $demandData['acreedor_cif']);
      $stmt->bindParam(':acreedor_domicilio', $demandData['acreedor_domicilio']);
      $stmt->bindParam(':acreedor_telefono', $demandData['acreedor_telefono']);
      $stmt->bindParam(':acreedor_fax', $demandData['acreedor_fax']);
      $stmt->bindParam(':acreedor_email', $demandData['acreedor_email']);
      $stmt->bindParam(':deudor_nombre', $demandData['deudor_nombre']);
      $stmt->bindParam(':deudor_cif', $demandData['deudor_cif']);
      $stmt->bindParam(':deudor_domicilio', $demandData['deudor_domicilio']);
      $stmt->bindParam(':deudor_telefono', $demandData['deudor_telefono']);
      $stmt->bindParam(':deudor_fax', $demandData['deudor_fax']);
      $stmt->bindParam(':deudor_email', $demandData['deudor_email']);
      $stmt->bindParam(':importe_total_deuda', $demandData['importe_total_deuda']);
      $stmt->bindParam(':lista_facturas', $demandData['lista_facturas']);
      $stmt->bindParam(':concepto', $demandData['concepto']);
      $stmt->bindParam(':created_at', $demandData['created_at']);
      file_put_contents($this->autofilling, "inicio exec\n", FILE_APPEND);
      file_put_contents($this->autofilling, "Demanda añadida a la base de datos: " . json_encode($demandData) . "\n", FILE_APPEND);
      $stmt->execute();
      file_put_contents($this->autofilling, "fin exec\n", FILE_APPEND);
    } catch (Exception $e) {
      file_put_contents($this->autofilling, "Error al añadir demanda: " . json_encode($e->getMessage()) . "\n", FILE_APPEND);
      error_log("Failed to add demand: " . $e->getMessage());
      throw new Exception("Failed to add demand");
    }
  }
  public function getDemand($uuid_document)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT * FROM demandas WHERE document_uuid = :document_uuid");
      $stmt->bindParam(':document_uuid', $uuid_document);
      $stmt->execute();
      return $stmt->fetchAll();
    } catch (Exception $e) {
      error_log("Failed to get demand: " . $e->getMessage());
      throw new Exception("Failed to get demand");
    }
  }

  public function getDemandById($id)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT * FROM demandas WHERE id = :id");
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      return $stmt->fetch();
    } catch (Exception $e) {
      error_log("Failed to get demand: " . $e->getMessage());
      throw new Exception("Failed to get demand");
    }
  }

  public function changeStatus($uuid_document, $newStatus)
  {
    try {
      $stmt = $this->pdo->prepare("UPDATE demandas SET status = :status WHERE document_uuid = :document_uuid");
      $stmt->bindParam(':status', $newStatus);
      $stmt->bindParam(':document_uuid', $uuid_document);
      $stmt->execute();
    } catch (Exception $e) {
      error_log("Failed to change status: " . $e->getMessage());
      throw new Exception("Failed to change status");
    }
  }

  public function getStatus($uuid_document)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT status FROM demandas WHERE document_uuid = :document_uuid");
      $stmt->bindParam(':document_uuid', $uuid_document);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (Exception $e) {
      error_log("Failed to get status: " . $e->getMessage());
      throw new Exception("Failed to get status");
    }
  }

  // Add your methods for handling "demandas" here
}
