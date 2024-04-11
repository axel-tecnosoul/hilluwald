<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once 'database.php';
$pdo = Database::connect();

$id_despacho=$_GET["id_despacho"];

$aContenedoresDespachados=[];

//INICIO OBTENCION DE REGISTROS A MOSTRAR EN LA TABLA

//obtenemos los contenedores despachados
$sql = "SELECT dc.id AS id_despacho_contenedor,c.id AS id_contenedor,tc.tipo,c.cantidad_orificios,c.ancho,c.alto,c.largo,dc.cantidad_despachada,dc.cantidad_devuelta,dc.requiere_devolucion FROM despachos_contenedores dc INNER JOIN contenedores c ON dc.id_contenedor=c.id INNER JOIN tipos_contenedores tc ON c.id_tipo_contenedor=tc.id WHERE dc.id_despacho=".$id_despacho;
//echo $sql;
foreach ($pdo->query($sql) as $row) {
  $contenedor=$row["tipo"]." ".$row["cantidad_orificios"]."u. ".$row["ancho"]."x".$row["alto"]."x".$row["largo"];

  $aContenedoresDespachados[]=[
    "id_despacho_contenedor"=>$row["id_despacho_contenedor"],
    "id_contenedor"=>$row["id_contenedor"],
    "contenedor"=>$contenedor,
    "requiere_devolucion"=>$row["requiere_devolucion"],
    "cantidad_despachada"=>$row["cantidad_despachada"],
    "cantidad_devuelta"=>$row["cantidad_devuelta"],
  ];
}

Database::disconnect();

echo json_encode($aContenedoresDespachados);