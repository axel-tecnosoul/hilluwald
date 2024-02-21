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
  header("Location: listarClientes.php");
}

try {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "DELETE from clientes WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  Database::disconnect();
  
  header("Location: listarClientes.php");

} catch (PDOException $e) {
  if($e->getCode()==23000){?>
    El cliente no se puede eliminar porque está siendo utilizando en otras tablas de la base de datos
    <br><br>
    <input type='button' onclick='window.location.href="listarClientes.php"' value='Volver'><?php
  }else{
    echo 'Ha ocurrido un error: <br><br>' . $e->getMessage();?>
    <br><br>
    <input type='button' onclick='window.location.href="listarClientes.php"' value='Volver'><?php
    //var_dump($e);
  }
  
}