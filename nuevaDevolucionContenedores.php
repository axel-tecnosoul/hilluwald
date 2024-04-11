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
  $fecha=$_POST['fecha_devolucion_contenedores'];
  $id_despacho=$_POST['id_despacho_devolucion_contenedores'];

  $sql = "INSERT INTO devolucion_contenedores (id_cliente, fecha, id_despacho, id_usuario) VALUES (?,?,?,?)";
  $q = $pdo->prepare($sql);
  $params=array($id_cliente, $fecha, $id_despacho, $_SESSION['user']['id']);
  $q->execute($params);
  $id_devolucion_contenedores = $pdo->lastInsertId();

  $aDebug[]=[
    "consulta"=>$sql,
    "params"=>$params,
    "afe"=>$q->rowCount(),
  ];

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }
  
  //$cantPrendas = count($_POST["id_cultivo"]);

  $aProductos=[];

  $cantProdOK=0;
  foreach ($_POST['id_despacho_contenedor'] as $key => $id_despacho_contenedor) {

    $id_cultivo=NULL;

    $cantidad = $_POST['cantidad_devolver'][$key];
    $id_contenedor = $_POST['id_contenedor'][$key];
    $id_despacho_contenedor = $_POST['id_despacho_contenedor'][$key];

    if($cantidad>0){

      $aProductos[]=[
        "id_contenedor"=>$id_contenedor,
        "cantidad"=>$cantidad,
      ];

      $sql = "INSERT INTO devolucion_contenedores_detalle (id_devolucion_contenedores, id_contenedor, cantidad_devuelta) VALUES (?,?,?)";
      $q = $pdo->prepare($sql);
      $params=array($id_devolucion_contenedores,$id_contenedor,$cantidad);
      $result = $q->execute($params);
      //var_dump($result);
      
      if ($result === false) {
        // Si ocurrió un error, obtener información sobre el error
        $errorInfo = $q->errorInfo();
        // El mensaje de error se encuentra en el índice 2 del array
        $errorMessage = $errorInfo[2];
        // Mostrar el mensaje de error o realizar alguna otra acción
        echo "Error al ejecutar la consulta: $errorMessage";
      } else {
        // La consulta se ejecutó correctamente
        $afe = $q->rowCount();
      }

      if($afe==1){
        $cantProdOK++;
      }

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      $aDebug[]=[
        "consulta"=>$sql,
        "params"=>$params,
        "afe"=>$q->rowCount(),
      ];

      $sql = "UPDATE despachos_contenedores SET cantidad_devuelta = ? WHERE id = ?";
      $q = $pdo->prepare($sql);
      $params=array($cantidad,$id_despacho_contenedor);
      $result = $q->execute($params);

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      $aDebug[]=[
        "consulta"=>$sql,
        "params"=>$params,
        "afe"=>$q->rowCount(),
      ];

    }

  }

  if ($modoDebug==1) {
    var_dump($aProductos);
    echo "cantProdOK!=count(aProductos)<br>";
    echo $cantProdOK."!=".count($aProductos)."<br>";
    var_dump($cantProdOK!=count($aProductos));
    echo "<br><br>";
  }

  $ok=1;
  if($cantProdOK!=count($aProductos)){
    $ok=0;
    var_dump($aDebug);
  }

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