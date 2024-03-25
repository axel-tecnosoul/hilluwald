<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php';
include 'funciones.php';
session_start();
$adespachos=[];

$pdo = Database::connect();
$columns = $_GET['columns'];


$data_columns = $fields = ['d.id','date_format(d.fecha,"%d/%m/%Y")', 'c.razon_social', 'd.id_pedido', 'd.id_cliente_retira','d.campana','d.id_transporte', 'ch.nombre_apellido','ve.descripcion', 'd.patente2', 'lo.nombre', 'd.id_plantador', 'l.localidad', 'd.id_usuario', 'd.fecha_hora_alta'];

$from="FROM despachos d INNER JOIN despachos_detalle dd ON dd.id_despacho= d.id LEFT JOIN clientes c ON c.id = d.id_cliente INNER JOIN especies es ON dd.id_cultivo=es.id INNER JOIN transportes t ON d.id_transporte=t.id INNER JOIN choferes ch ON d.id_chofer=ch.id LEFT JOIN lotes lo ON d.id_lote = lo.id INNER JOIN localidades l ON d.id_localidad = l.id  INNER JOIN vehiculos ve ON d.id_vehiculo=ve.id INNER JOIN cultivos cu ON dd.id_cultivo=cu.id INNER JOIN especies e ON dd.id_especie=e.id INNER JOIN procedencias_especies pr ON dd.id_procedencia=pr.id";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

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

/*$tipo_comprobante=$_GET["tipo_comprobante"];
$filtroTipoComprobante="";
if($tipo_comprobante!=""){
  $ex=explode(",",$tipo_comprobante);
  $tipo_comprobante="'".implode("','",$ex)."'";
  $filtroTipoComprobante=" AND d.facturacion IN ($tipo_comprobante)";
}*/

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "d.id IS NOT NULL";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_sucursal']; 
}
$group_by=" GROUP BY d.id";
$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroCliente;//.$filtroTipoComprobante;

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
  $where .= '('.implode(' OR ', $aWhere).')';
}

$where.=$group_by;
$whereFiltered.=$group_by;

$length = $_GET['length'];
$start = $_GET['start'];
//OBTENEMOS EL TOTAL DE REGISTROS
$countSql = "SELECT count(d.id) as Total $from WHERE $where";
$countSt = $pdo->query($countSql);
$total=0;
//$cant=$pdo->rowCount();
/*var_dump($countSt);
if($cant>0){
  $total = $countSt->fetch()['Total'];
}*/
// Verificar si la consulta se ejecutó correctamente y devolvió un valor numérico válido
if ($countSt !== false) {
  // Obtener el resultado de la consulta
  $countResult = $countSt->fetchColumn();
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


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(d.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);
//echo $queryFiltered;

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

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','d.activo','d.id'];

//$sql2 = "SELECT SUM(CASE WHEN d.tipo_comprobante IN ('NCA','NCB') THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
/*$sql2 = "SELECT SUM(CASE WHEN d.facturacion='nota_credito' THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();

$total_facturas_recibos = ($row2['total_facturas_recibos'] ?: 0);*/

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//echo $sql;
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
    foreach ($pdo->query($sql) as $row) {

      //['d.id','date_format(d.fecha_hora,"%d/%m/%Y %H:%i")','d.facturacion','c.razon_social','d.total'];//,'d.estado'

      //var_dump($row);
      $adespachos[]=[
        "id_despacho"=>$row['id'],
        "fecha"=>$row[1],// AS fecha_hora
        "id_pedido"=>$row["id_pedido"],
        "razon_social"=>$row["razon_social"],
        "id_cliente_retira"=>$row["id_cliente_retira"],
        "campana"=>$row["campana"],
        "id_transporte"=>$row["id_transporte"],
        "nombre_apellido"=>$row["nombre_apellido"],
        "descripcion"=>$row["descripcion"],
        "patente2"=>$row["patente2"],
        "nombre"=>$row["nombre"],
        "id_plantador"=>$row["id_plantador"],
        "localidad"=>$row["localidad"],
        "id_usuario"=>$row["id_usuario"],
        "fecha_hora_alta"=>$row["fecha_hora_alta"]
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
      //'total_facturas_recibos'=>$total_facturas_recibos,
    ];
} else {
    var_dump($pdo->errorInfo());
    die;
}

echo json_encode([
  'data' => $adespachos,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($adespachos),
  'queryInfo'=>$queryInfo,
]);