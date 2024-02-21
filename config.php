<?php 
// These variables define the connection information for your MySQL database 
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hilluwald";

$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
try { $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); } 
catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());} 
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
header('Content-Type: text/html; charset=utf-8'); 
session_start(); 

$aCondicionesFiscales=["IVA Responsable Inscripto","IVA Responsable no Inscripto","IVA no Responsable","IVA Sujeto Exento","Consumidor Final","Responsable Monotributo","Sujeto no Categorizado","Proveedor del Exterior","Cliente del Exterior","IVA Liberado – Ley Nº 19.640","IVA Responsable Inscripto – Agente de Percepción","Pequeño Contribuyente Eventual","Monotributista Social","Pequeño Contribuyente Eventual Social"];
?>