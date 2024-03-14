<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarTransportes.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

  $pdo->beginTransaction();
  
  $sql = "DELETE from choferes WHERE id_transporte = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));

  $sql = "DELETE from vehiculos WHERE id_transporte = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  
  $sql = "DELETE from transportes WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));

  // Confirmar la transacción
  $pdo->commit();
  
  Database::disconnect();
  
  header("Location: listarTransportes.php");

} catch (PDOException $e) {
  
  // Si algo falla, hacer rollback
  $pdo->rollBack();
  Database::disconnect();

  if($e->getCode()==23000){?>
    El Transporte no se puede eliminar porque está siendo utilizando en otras tablas de la base de datos
    <br><br>
    <input type='button' onclick='window.location.href="listarTransportes.php"' value='Volver'><?php
  }else{
    echo 'Ha ocurrido un error: <br><br>' . $e->getMessage();?>
    <br><br>
    <input type='button' onclick='window.location.href="listarTransportes.php"' value='Volver'><?php
    //var_dump($e);
  }
  
}