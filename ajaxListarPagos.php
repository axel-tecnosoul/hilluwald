<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php';
include 'funciones.php';
session_start();
$aPedidos=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

$data_columns = $fields = ['p.id','date_format(p.fecha,"%d/%m/%Y")','c.razon_social','p.monto_total'];//,'p.estado'

$from="FROM pagos p INNER JOIN pagos_detalle pd ON pd.id_pago=p.id LEFT JOIN clientes c ON c.id = p.id_cliente INNER JOIN especies e ON pd.id_especie=e.id INNER JOIN servicios s ON pd.id_servicio=s.id";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde=" AND DATE(p.fecha)>='$desde'";
}

$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta=" AND DATE(p.fecha)<='$hasta'";
}

$id_cliente=$_GET["id_cliente"];
$filtroCliente="";
if($id_cliente!=0 and $id_cliente!=""){
  $filtroCliente=" AND p.id_cliente IN ($id_cliente)";
}

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "p.anulado = 0";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_sucursal']; 
}
$group_by=" GROUP BY p.id";

foreach ($columns as $k => $column) {
    if ($search = $column['search']['value']) {
        $where .= ' AND '.$fields[$k].' = '.$search;
    }
}

//$where = substr($where, 0, -5);

$globalSearch = $_GET['search'];
/*if ( $globalSearchValue = $globalSearch['value'] ) {
	$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
}*/
if ( $globalSearchValue = $globalSearch['value'] ) {
  $aWhere=[];
  foreach ($fields as $k => $field) {
    $aWhere[]=$field.' LIKE "%'.$globalSearchValue.'%"';
    //$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
  }
  $where .= ' AND ('.implode(' OR ', $aWhere).')';
}

$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroCliente;//.$filtroTipoComprobante;

$where.=$group_by;
$whereFiltered.=$group_by;

$length = $_GET['length'];
$start = $_GET['start'];

//OBTENEMOS EL TOTAL DE REGISTROS
$sqlCountTotal = "SELECT count(p.id) as Total $from WHERE $where";
$stCountTotal = $pdo->query($sqlCountTotal);
//echo $countSql;
//$total = $stCountTotal->fetch()['Total'];
$total=0;
// Verificar si la consulta se ejecutó correctamente y devolvió un valor numérico válido
if ($stCountTotal !== false) {
  // Obtener el resultado de la consulta
  $countResult = $stCountTotal->fetchColumn();
  // Comprobar si el resultado es numérico
  if (is_numeric($countResult)) {
      // El resultado es un valor numérico válido. Puedes utilizar $countResult en tu código
      //echo "El resultado es: " . $countResult;
      $total = $countResult;
  } else {
      // La consulta no devolvió un valor numérico válido
      //echo "La consulta no devolvió un valor numérico válido";
  }
} else {
  // Ocurrió un error al ejecutar la consulta
  //echo "Ocurrió un error al ejecutar la consulta SQL";
}
$total=$stCountTotal->rowCount();


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(p.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
$resFilterLength = $pdo->query($queryFiltered);
//$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];
$recordsFiltered = 0;
// Verificar si la consulta se ejecutó correctamente y devolvió un valor numérico válido
if ($resFilterLength !== false) {
  // Obtener el resultado de la consulta
  $countResult = $resFilterLength->fetchColumn();
  // Comprobar si el resultado es numérico
  if (is_numeric($countResult)) {
      // El resultado es un valor numérico válido. Puedes utilizar $countResult en tu código
      //echo "El resultado es: " . $countResult;
      $recordsFiltered = $countResult;
  } else {
      // La consulta no devolvió un valor numérico válido
      //echo "La consulta no devolvió un valor numérico válido";
  }
} else {
  // Ocurrió un error al ejecutar la consulta
  //echo "Ocurrió un error al ejecutar la consulta SQL";
}

$recordsFiltered=$resFilterLength->rowCount();


/*$sql2 = "SELECT SUM(CASE WHEN p.facturacion='nota_credito' THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();

$total_facturas_recibos = ($row2['total_facturas_recibos'] ?: 0);*/

$campos=implode(",", $fields);
$sql2 = "SELECT SUM(p.monto_total) AS total FROM pagos p WHERE p.anulado=0 $filtroDesde $filtroHasta $filtroCliente ";
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();
$total_pagos = ($row2['total'] ?: 0);

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos,GROUP_CONCAT('+',FORMAT(pd.cantidad_plantines,0,'de_DE'),' ',s.servicio,' ',e.especie,' x $ ',FORMAT(pd.precio_unitario, 2, 'es_AR'),' c/u ($ ',FORMAT(pd.subtotal, 2, 'es_AR'),')' SEPARATOR '<br>') AS detalle_cultivos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//echo $sql;
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
  //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
  foreach ($pdo->query($sql) as $row) {

    //['p.id','date_format(p.fecha_hora,"%d/%m/%Y %H:%i")','p.facturacion','c.razon_social','p.total'];//,'p.estado'

    $aPedidos[]=[
      "id_pedido"=>$row['id'],
      "fecha"=>$row[1],// AS fecha_hora
      //"tipo_comprobante"=>$tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]),
      "razon_social"=>$row["razon_social"],
      "monto_total"=>$row["monto_total"],
      "detalle_cultivos"=>$row["detalle_cultivos"]
      //"estado"=>$row['estado']
    ];
  }

  $queryInfo=[
    'campos' => $campos,
    'from' => $from,
    'where' => $whereFiltered,
    'orderBy' => $orderBy,
    'length' => $length,
    'start' => $start,
    'query' => $sql,
    'total_pagos'=>$total_pagos,
  ];
} else {
  var_dump($pdo->errorInfo());
  die;
}

echo json_encode([
  'data' => $aPedidos,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aPedidos),
  'queryInfo'=>$queryInfo,
  'sqlCountTotal'=>$sqlCountTotal,
  'queryFiltered'=>$queryFiltered,
]);