<?php
require("config.php");
require 'database.php';

try {
  
  $notas=$_POST["nota"];
  $idCliente=$_POST["idCliente"];
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "UPDATE clientes SET notas = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $resultado = $q->execute(array($notas,$idCliente));
  
  Database::disconnect();
  
  // Verificar si la consulta se ejecutó correctamente
  if ($resultado) {
    echo 1;
  } else {
    echo "Hubo un error al ejecutar la consulta";
  }

} catch (PDOException $e) {
  if($e->getCode()==23000){
    
  }else{
    echo 'Ha ocurrido un error: <br><br>' . $e->getMessage();
  }
  
}