<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
include_once("funciones.php");

if ( !empty($_POST)) {
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;
  $aDebug=[];

  $pdo->beginTransaction();
  
  if ($modoDebug==1) {
    var_dump($_POST);
    var_dump($_GET);
  }

  $id_cliente=$_GET['id_cliente'];
  $fecha=$_POST['fecha_pedido'];
  $campana=$_POST['campana_pedido'];
  $sucursal=$_POST['sucursal_pedido'];
  $observaciones=$_POST['observaciones_pedido'];

  $aDetallePedido=[];
  foreach ($_POST['id_servicio'] as $indice => $id_servicio) {
    $aDetallePedido[] = [
      'id_servicio' => $id_servicio,
      'id_especie' => $_POST['id_especie'][$indice],
      'id_procedencia' => $_POST['id_procedencia'][$indice],
      'id_material' => $_POST['id_material'][$indice],
      'cantidad' => $_POST['cantidad'][$indice],
      'plantines_retirados' => 0,
    ];
  }

  $ok=insertPedido($id_cliente,$fecha,$campana,$sucursal,$observaciones,$id_despacho=NULL,$aDetallePedido);

  if($ok==0){
    $pdo->rollback();
    die("Ha ocurrido un error al cargar el pedido");
  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    echo "ok: ".$ok;
    die();
  }

  $pdo->commit();
  Database::disconnect();
  
  header("Location: verCliente.php?id=".$_GET["id_cliente"]);
}