<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once 'database.php';
$pdo = Database::connect();

$id_pedido=$_GET["id_pedido"];

$aCtaCte=[];

//INICIO OBTENCION DE REGISTROS A MOSTRAR EN LA TABLA

//obtenemos los pedidos
$sql = " SELECT * FROM pedidos_detalle pd WHERE pd.id_pedido=".$id_pedido;
//echo $sql;
foreach ($pdo->query($sql) as $row) {
  $aCtaCte[]=[
    "cantidad_plantines"=>$row["cantidad_plantines"],
    "fecha_hora_alta"=>$row["fecha_hora_alta"],
    "id"=>$row["id"],
    "id_cultivo"=>$row["id_cultivo"],
    "id_especie"=>$row["id_especie"],
    "id_material"=>$row["id_material"],
    "id_pedido"=>$row["id_pedido"],
    "id_procedencia"=>$row["id_procedencia"],
    "plantines_pagados"=>$row["plantines_pagados"],
    "plantines_retirados"=>$row["plantines_retirados"],
  ];
    /*"tipo_comprobante"=>"Pedido",
    "id_pedido"=>$row['id'],
    "fecha"=>$row['fecha'],// AS fecha_hora
    "campana"=>$row["campana"],
    "cantidad"=>$row['cantidad_plantines'],
    "fecha_hora_alta"=>$row['fecha_hora_alta'],
  ];*/
}

Database::disconnect();

echo json_encode($aCtaCte);