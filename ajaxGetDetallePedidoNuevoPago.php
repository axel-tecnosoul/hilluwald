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
$sql = "SELECT pd.id AS id_pedido_detalle,pd.id_servicio,pd.id_especie,pd.id_procedencia,pd.id_material,s.servicio,e.especie,pe.procedencia,c.material,pd.cantidad_plantines,pd.plantines_retirados FROM pedidos_detalle pd INNER JOIN servicios s ON pd.id_servicio=s.id INNER JOIN especies e ON pd.id_especie=e.id LEFT JOIN procedencias_especies pe ON pd.id_procedencia=pe.id LEFT JOIN cultivos c ON pd.id_material=c.id WHERE pd.id_pedido=".$id_pedido;
//echo $sql;
foreach ($pdo->query($sql) as $row) {
  $id_especie=$row["id_especie"];
  $id_procedencia=$row["id_procedencia"];
  $id_material=$row["id_material"];
  
  $aProcedencias=[];
  if(empty($id_procedencia)){
    $sql2 = "SELECT p.id,p.procedencia FROM cultivos c INNER JOIN procedencias_especies p ON c.id_procedencia=p.id WHERE id_especie=$id_especie GROUP BY p.id";
    //echo $sql2;
    foreach ($pdo->query($sql2) as $row2) {
      $aProcedencias[]=[
        "id"=>$row2["id"],
        "procedencia"=>$row2["procedencia"],
      ];
    }
  }

  $aMateriales=[];
  if(empty($id_material) and $id_procedencia>0){
    $sql3 = "SELECT id,material FROM cultivos WHERE id_especie=$id_especie AND id_procedencia=$id_procedencia";
    //echo $sql3;
    foreach ($pdo->query($sql3) as $row3) {
      $aMateriales[]=[
        "id"=>$row3["id"],
        "material"=>$row3["material"],
      ];
    }
  }

  $aCtaCte[]=[
    "id_pedido_detalle"=>$row["id_pedido_detalle"],
    "id_servicio"=>$row["id_servicio"],
    "id_especie"=>$id_especie,
    "id_procedencia"=>$id_procedencia,
    "id_material"=>$id_material,
    "servicio"=>$row["servicio"],
    "especie"=>$row["especie"],
    "procedencia"=>$row["procedencia"] ?: "",
    "aProcedencias"=>$aProcedencias,
    "material"=>$row["material"] ?: "",
    "aMateriales"=>$aMateriales,
    "cantidad_plantines"=>$row["cantidad_plantines"],
    "plantines_retirados"=>$row["plantines_retirados"],
  ];
}

Database::disconnect();

echo json_encode($aCtaCte);