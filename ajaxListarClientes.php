<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php';
include 'funciones.php';
session_start();

$pdo = Database::connect();

$sql = "SELECT c.id,c.razon_social,c.cuit,c.cond_fiscal,c.direccion,c.email, c.telefono, c.notas, l.localidad, c.activo FROM clientes c LEFT JOIN localidades l ON l.id = c.id_localidad";
error_log($sql);

$aClientes=[];
foreach ($pdo->query($sql) as $row) {
  
  $activo = 'No';
  if($row["activo"] == 1){
    $activo = 'Si';
  }

  $aClientes[]=[
    "id_cliente"=>$row['id'],
    "razon_social"=>$row["razon_social"],
    "cond_fiscal"=>$row["cond_fiscal"],
    "cuit"=>$row["cuit"],
    "telefono"=>$row["telefono"],
    //"localidad"=>$row["localidad"],
    "activo"=>$activo,
  ];
}

echo json_encode($aClientes);