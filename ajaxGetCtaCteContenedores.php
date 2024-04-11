<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once 'database.php';
$pdo = Database::connect();

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde=" AND DATE(d.fecha)>='$desde'";
}

$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta=" AND DATE(d.fecha)<='$hasta'";
}

$id_cliente=$_GET["id_cliente"];
$filtroCliente="";
if($id_cliente!=0 and $id_cliente!=""){
  $filtroCliente=" AND d.id_cliente IN ($id_cliente)";
}

$id_contenedor=$_GET["id_contenedor"];
$filtroContenedor="";
if($id_contenedor!=0 and $id_contenedor!=""){
  $filtroContenedor=" AND dc.id_contenedor IN ($id_contenedor)";
}

function encerrar_entre_comillas($valor) {
  return '"' . addslashes($valor) . '"';
}

$aCtaCte=[];
//if($desde<=$hasta and $id_almacen!=0){
if($desde<=$hasta){

  //INICIO SALDO ANTERIOR
  
  //FIN SALDO ANTERIOR

  //INICIO OBTENCION DE REGISTROS A MOSTRAR EN LA TABLA

  //obtenemos los pedidos
  //$sql = " SELECT p.id,date_format(p.fecha,'%d/%m/%Y') AS fecha,p.campana,pd.cantidad_plantines,p.fecha_hora_alta FROM pedidos p INNER JOIN pedidos_detalle pd ON pd.id_pedido=p.id WHERE p.anulado=0 $filtroDesde $filtroHasta $filtroCliente $filtroCultivo";
  $sql = "SELECT dc.id AS id_despacho_contenedor,date_format(d.fecha,'%d/%m/%Y') AS fecha,dc.id_despacho,tc.tipo,c.cantidad_orificios,c.ancho,c.alto,dc.cantidad_despachada,dc.cantidad_devuelta AS total_cantidad_devuelta FROM despachos_contenedores dc INNER JOIN despachos d ON dc.id_despacho=d.id INNER JOIN contenedores c ON dc.id_contenedor=c.id INNER JOIN tipos_contenedores tc ON c.id_tipo_contenedor=tc.id WHERE d.anulado=0 $filtroDesde $filtroHasta $filtroCliente $filtroContenedor";
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    
    $aCtaCte[]=[
      "tipo_comprobante"=>"Despacho",
      "id_despacho_contenedor"=>$row['id_despacho_contenedor'],
      "fecha"=>$row['fecha'],// AS fecha_hora
      "id_despacho"=>$row["id_despacho"],
      "tipo"=>$row["tipo"],
      "cantidad_orificios"=>$row["cantidad_orificios"] ?: "",
      "ancho"=>$row["ancho"] ?: "",
      "alto"=>$row['alto'],
      "cantidad"=>$row['cantidad_despachada'],
      "total_cantidad_devuelta"=>$row['total_cantidad_devuelta'],
    ];
  }

  //obtenemos los despachos
  $sql = "SELECT d.id AS id_devolucion,dc.id AS id_despacho_contenedor,d.id_despacho,date_format(d.fecha,'%d/%m/%Y') AS fecha,dc.cantidad_devuelta,d.fecha_hora_alta FROM devolucion_contenedores d INNER JOIN devolucion_contenedores_detalle dc ON dc.id_devolucion_contenedores=d.id WHERE 1 $filtroDesde $filtroHasta $filtroCliente $filtroContenedor";//p.anulado=0 
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {

    $aCtaCte[]=[
      "tipo_comprobante"=>"Devolucion",
      "id_despacho"=>$row['id_despacho'],
      "id_despacho_contenedor"=>$row['id_despacho_contenedor'],
      "fecha"=>$row['fecha'],// AS fecha_hora
      "id_devolucion"=>$row["id_devolucion"],
      /*"tipo"=>$row["tipo"],
      "cantidad_orificios"=>$row["cantidad_orificios"] ?: "",
      "ancho"=>$row["ancho"] ?: "",
      "alto"=>$row['alto'],*/
      "cantidad"=>$row['cantidad_devuelta'],
    ];
  }
  
  Database::disconnect();

  function date_compare($a, $b){
    // Comparar por id_despacho
    if ($a['id_despacho'] != $b['id_despacho']) {
      return $a['id_despacho'] - $b['id_despacho'];
    }

    // Si los id_despacho son iguales, comparar por fecha
    $t1 = strtotime($a['fecha']);
    $t2 = strtotime($b['fecha']);

    if ($t1 == $t2) {
        // Si las fechas son iguales, comparar por fecha_hora_alta
        $t1_alta = strtotime($a['fecha_hora_alta']);
        $t2_alta = strtotime($b['fecha_hora_alta']);
        return $t1_alta - $t2_alta;
    } else {
        return $t1 - $t2;
    }
  }

  usort($aCtaCte, 'date_compare');

  foreach ($aCtaCte as $key => $value) {
    //$aCtaCte[$key]['fecha']=date("d/m/Y H:i",strtotime($value['fecha']));
    switch ($value["tipo_comprobante"]) {
      case 'Despacho':
        $cantidad_despachada=$aCtaCte[$key]['cantidad'];
        $saldo_contenedores=$cantidad_despachada;
        if($key>0){
          $saldo_contenedores=$aCtaCte[$key-1]["saldo_contenedores"]+$cantidad_despachada;
        }

        $aCtaCte[$key]["cantidad_despachada"]=$cantidad_despachada;
        $aCtaCte[$key]["cantidad_devuelta"]=0;
        $aCtaCte[$key]["saldo_contenedores"]=$saldo_contenedores;
        break;
      case 'Devolucion':
        $cantidad_devuelta=$aCtaCte[$key]['cantidad'];
        $saldo_contenedores=$cantidad_devuelta;
        if($key>0){
          $saldo_contenedores=$aCtaCte[$key-1]["saldo_contenedores"]-$cantidad_devuelta;
        }

        $aCtaCte[$key]["cantidad_despachada"]=0;
        $aCtaCte[$key]["cantidad_devuelta"]=$cantidad_devuelta;
        $aCtaCte[$key]["saldo_contenedores"]=$saldo_contenedores;
        break;
    }
  }
}

echo json_encode($aCtaCte);