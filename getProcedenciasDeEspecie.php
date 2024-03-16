<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once 'database.php';
$pdo = Database::connect();

$id_especie=$_POST["id_especie"];

$aProcedencias=[];

//obtenemos los pedidos
$sql = "SELECT p.id,p.procedencia FROM cultivos c INNER JOIN procedencias_especies p ON c.id_procedencia=p.id WHERE id_especie=$id_especie GROUP BY p.id";
//echo $sql;
foreach ($pdo->query($sql) as $row) {
  $aProcedencias[]=[
    "id"=>$row["id"],
    "procedencia"=>$row["procedencia"],
  ];
}

Database::disconnect();

echo json_encode($aProcedencias);