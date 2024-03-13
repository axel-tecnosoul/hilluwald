<?php
    require("config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: index.php");
        die("Redirecting to index.php"); 
    }
	
	require 'database.php';

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( null==$id ) {
		header("Location: listarPaises.php");
	}
	
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	try {
		$sql = "DELETE FROM `paises` WHERE id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		
		Database::disconnect();
			
		header("Location: listarPaises.php");
	} catch (PDOException $e) {
	if($e->getCode()==23000){?>
	  El Pais no se puede eliminar porque está siendo utilizando en otras tablas de la base de datos
	  <br><br>
	  <input type='button' onclick='window.location.href="listarPaises.php"' value='Volver'><?php
	}else{
	  echo 'Ha ocurrido un error: <br><br>' . $e->getMessage();?>
	  <br><br>
	  <input type='button' onclick='window.location.href="listarPaises.php"' value='Volver'><?php
	  //var_dump($e);
	}
	
  }
?>