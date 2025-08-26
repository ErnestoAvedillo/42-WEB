<?php
require_once __DIR__ . '/../database/pg_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Demandas
{

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

  public function addDemand($demandData)
  {
    try {
      $stmt = $this->pdo->prepare("
            INSERT INTO demandas (user_uuid,
            document_uuid,
            status,
            acreedor_nombre,
            acreedor_CIF,
            acreedor_domicilio,
            acreedor_telefono,
            acreedor_FAX,
            acreedor_email,
            deudor_nombre,
            deudor_CIF,
            deudor_domicilio,
            deudor_telefono,
            deudor_FAX,
            deudor_email,
            cuantia,
            concepto,
            documentos,
            created_at) 
            VALUES (
            :user_uuid,
            :document_uuid,
            :status,
            :acreedor_nombre,
            :acreedor_CIF,
            :acreedor_domicilio,
            :acreedor_telefono,
            :acreedor_FAX,
            :acreedor_email,
            :deudor_nombre,
            :deudor_CIF,
            :deudor_domicilio,
            :deudor_telefono,
            :deudor_FAX,
            :deudor_email,
            :cuantia,
            :concepto,
            :documentos,
            :created_at)");
      $stmt->bindParam(':user_uuid', $demandData['user_uuid']);
      $stmt->bindParam(':document_uuid', $demandData['document_uuid']);
      $stmt->bindParam(':status', $demandData['status']);
      $stmt->bindParam(':acreedor_nombre', $demandData['acreedor_nombre']);
      $stmt->bindParam(':acreedor_CIF', $demandData['acreedor_CIF']);
      $stmt->bindParam(':acreedor_domicilio', $demandData['acreedor_domicilio']);
      $stmt->bindParam(':acreedor_telefono', $demandData['acreedor_telefono']);
      $stmt->bindParam(':acreedor_FAX', $demandData['acreedor_FAX']);
      $stmt->bindParam(':acreedor_email', $demandData['acreedor_email']);
      $stmt->bindParam(':deudor_nombre', $demandData['deudor_nombre']);
      $stmt->bindParam(':deudor_CIF', $demandData['deudor_CIF']);
      $stmt->bindParam(':deudor_domicilio', $demandData['deudor_domicilio']);
      $stmt->bindParam(':deudor_telefono', $demandData['deudor_telefono']);
      $stmt->bindParam(':deudor_FAX', $demandData['deudor_FAX']);
      $stmt->bindParam(':deudor_email', $demandData['deudor_email']);
      $stmt->bindParam(':cuantia', $demandData['cuantia']);
      $stmt->bindParam(':concepto', $demandData['concepto']);
      $stmt->bindParam(':documentos', $demandData['documentos']);
      $stmt->bindParam(':created_at', $demandData['created_at']);
      $stmt->execute();
    } catch (Exception $e) {
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
