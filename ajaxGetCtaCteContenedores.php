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
  /*
  $filtroHastaSaldoAnterior="AND DATE(fecha_hora)<'$desde'";

  $aCtaCte[]=[
    "id_venta"=>0,
    "id"=>"Saldo anterior",
    "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
    "motivo"=>"",
    "detalle"=>"",
    "forma_pago"=>"",
    "credito"=>0,
    "debito"=>0,
    "saldo"=>$saldo_anterior,
    "detalle_productos"=>"",
  ];

  $modo_debug=0;
  //PARA PODER DEBUGUEAR MOSTRAMOS VARIABLES EN LA COLUMNA DETALLE
  if($modo_debug==1){
    $detalle="total_facturas_recibos: $total_facturas_recibos<br>ingresos_externos: $ingresos_externos<br>egresos_caja_chica: $egresos_caja_chica<br>total_pago_proveedores: $total_pago_proveedores<br>";
    $detalle.="data[total_ventas]: $data[total_ventas]<br>data2[ingresos_externos]: $data2[ingresos_externos]<br>data3[egresos_caja_chica]: $data3[egresos_caja_chica]<br>data4[total_pago_proveedores]: $data4[total_pago_proveedores]<br>";
    $aCtaCte[]=[
      "id_venta"=>0,
      "id"=>"",
      "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
      "motivo"=>"",
      "detalle"=>$detalle,
      "forma_pago"=>"",
      "credito"=>0,
      "debito"=>0,
      "saldo"=>0,
      "detalle_productos"=>"",
    ];
  }*/

  //FIN SALDO ANTERIOR

  //INICIO OBTENCION DE REGISTROS A MOSTRAR EN LA TABLA

  //obtenemos los pedidos
  //$sql = " SELECT p.id,date_format(p.fecha,'%d/%m/%Y') AS fecha,p.campana,pd.cantidad_plantines,p.fecha_hora_alta FROM pedidos p INNER JOIN pedidos_detalle pd ON pd.id_pedido=p.id WHERE p.anulado=0 $filtroDesde $filtroHasta $filtroCliente $filtroCultivo";
  $sql = "SELECT dc.id AS id_despacho_contenedor,date_format(d.fecha,'%d/%m/%Y') AS fecha,dc.id_despacho,tc.tipo,c.cantidad_orificios,c.ancho,c.alto,dc.cantidad_despachada,dc.cantidad_devuelta AS total_cantidad_devuelta FROM despachos_contenedores dc INNER JOIN despachos d ON dc.id_despacho=d.id INNER JOIN contenedores c ON dc.id_contenedor=c.id INNER JOIN tipos_contenedores tc ON c.id_tipo_contenedor=tc.id WHERE d.anulado=0 $filtroDesde $filtroHasta $filtroCliente $filtroContenedor";
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    
    //$iconVer="<a href='verMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    //$iconVer="<span data-id='".$row["id_movimiento"]."' data-tipo='movimiento' class='ver badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></span>";

    /*$iconEdit="";
    $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
    if($row["id_cierre_caja"]==0){
      $iconEdit="<a href='modificarMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-secondary'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
      $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
    }

    if($row["tipo_movimiento"]=="Ingreso"){
      $credito=$row["total"];
      $debito=0;
      $saldo=0;
    }else{
      $credito=0;
      $debito=$row["total"];
      $saldo=0;
    }*/
    $aCtaCte[]=[
      "tipo_comprobante"=>"Despacho",
      "id_despacho_contenedor"=>$row['id_despacho_contenedor'],
      "fecha"=>$row['fecha'],// AS fecha_hora
      "id_despacho"=>$row["id_despacho"],
      "tipo"=>$row["tipo"],
      "cantidad_orificios"=>$row["cantidad_orificios"] ?: "",
      "ancho"=>$row["ancho"] ?: "",
      "alto"=>$row['alto'],
      "cantidad_despachada"=>$row['cantidad_despachada'],
      "total_cantidad_devuelta"=>$row['total_cantidad_devuelta'],
    ];
  }

  $b=1;
  if($b=0){
  //obtenemos los despachos
  $sql = "SELECT p.id,p.id_pedido,date_format(p.fecha,'%d/%m/%Y') AS fecha,p.campana,pd.cantidad_plantines,p.fecha_hora_alta,s.servicio,pe.procedencia,c.material FROM despachos p INNER JOIN despachos_detalle pd ON pd.id_despacho=p.id INNER JOIN servicios s ON pd.id_servicio=s.id LEFT JOIN procedencias_especies pe ON pd.id_procedencia=pe.id LEFT JOIN cultivos c ON pd.id_material=c.id WHERE 1 $filtroDesde $filtroHasta $filtroCliente $filtroCultivo";//p.anulado=0 
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    
    //$iconVer="<a href='verMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    //$iconVer="<span data-id='".$row["id_movimiento"]."' data-tipo='movimiento' class='ver badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></span>";

    /*$iconEdit="";
    $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
    if($row["id_cierre_caja"]==0){
      $iconEdit="<a href='modificarMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-secondary'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
      $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
    }

    if($row["tipo_movimiento"]=="Ingreso"){
      $credito=$row["total"];
      $debito=0;
      $saldo=0;
    }else{
      $credito=0;
      $debito=$row["total"];
      $saldo=0;
    }*/
    /*$aCtaCte[]=[
      "tipo_comprobante"=>"Despacho",
      "id_despacho"=>$row['id'],
      "id_pedido"=>$row['id_pedido'],
      "fecha"=>$row['fecha'],// AS fecha_hora
      "campana"=>$row["campana"],
      "servicio"=>$row["servicio"],
      "procedencia"=>$row["procedencia"],
      "material"=>$row["material"],
      "cantidad"=>$row['cantidad_plantines'],
      "fecha_hora_alta"=>$row['fecha_hora_alta'],
    ];*/
  }
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
        $cantidad_despachada=$aCtaCte[$key]['cantidad_despachada'];
        $saldo_contenedores=$cantidad_despachada;
        if($key>0){
          $saldo_contenedores=$aCtaCte[$key-1]["saldo_contenedores"]+$cantidad_despachada;
        }

        $aCtaCte[$key]["cantidad_devuelta"]="";
        $aCtaCte[$key]["saldo_contenedores"]=$saldo_contenedores;
        break;
      case 'Despacho':
        $cantidad_devuelta=$aCtaCte[$key]['cantidad_devuelta'];
        $saldo_contenedores=$cantidad_devuelta;
        if($key>0){
          $saldo_contenedores=$aCtaCte[$key-1]["saldo_contenedores"]-$cantidad_devuelta;
        }

        $aCtaCte[$key]["cantidad_despacho"]="";
        $aCtaCte[$key]["cantidad_devuelta"]=$cantidad_devuelta;
        $aCtaCte[$key]["saldo_contenedores"]=$saldo_contenedores;
        break;
    }
  }
}

echo json_encode($aCtaCte);