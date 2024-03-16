<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once 'database.php';
$pdo = Database::connect();

$id_especie=$_POST["id_especie"];
$id_procedencia=$_POST["id_procedencia"];

$aMateriales=[];

//obtenemos los pedidos
$sql = "SELECT id,material FROM cultivos WHERE id_especie=$id_especie AND id_procedencia=$id_procedencia";
//echo $sql;
foreach ($pdo->query($sql) as $row) {
  $aMateriales[]=[
    "id"=>$row["id"],
    "material"=>$row["material"],
  ];
}

Database::disconnect();

echo json_encode($aMateriales);