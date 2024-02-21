<?php
/*
archivo de configuracion para los datos de facturacion electronica, espera la variable $homologacion para poder facturar en modo homolagcion
*/

$homologacion=1;

$cuit=20341491216;
$produccion=true;
$ruta="crt_maury_distribuciones_prod/";

if ($homologacion==1) {
  $cuit=20351290340;
  $produccion=false;
  $ruta="crt_axel_homo/";
}

$cert=$ruta."crt";
$key=$ruta."key";

$aInitializeAFIP=array('CUIT' => $cuit,'production'=>$produccion,'cert'=>$cert,'key'=>$key);