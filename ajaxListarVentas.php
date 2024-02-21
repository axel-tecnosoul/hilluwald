<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php';
include 'funciones.php';
session_start();
$aProductos=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

//$data_columns = ["","p.cb","p.codigo","c.categoria","p.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","p.precio","p.activo"];//PARA EL ORDENAMIENTO

$data_columns = $fields = ['v.id','date_format(v.fecha_venta,"%d/%m/%Y")','c.razon_social','v.facturacion','v.modalidad_pago','v.total'];//,'v.estado'

$from="FROM ventas v LEFT JOIN clientes c ON c.id = v.id_cliente";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde=" AND DATE(v.fecha_venta)>='$desde'";
}

$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta=" AND DATE(v.fecha_venta)<='$hasta'";
}

$id_cliente=$_GET["id_cliente"];
$filtroCliente="";
if($id_cliente!=0 and $id_cliente!=""){
  $filtroCliente=" AND v.id_cliente IN ($id_cliente)";
}

$tipo_comprobante=$_GET["tipo_comprobante"];
$filtroTipoComprobante="";
if($tipo_comprobante!=""){
  $ex=explode(",",$tipo_comprobante);
  $tipo_comprobante="'".implode("','",$ex)."'";
  $filtroTipoComprobante=" AND v.facturacion IN ($tipo_comprobante)";
}

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "v.anulada = 0";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_almacen']; 
}
$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroCliente.$filtroTipoComprobante;

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

$length = $_GET['length'];
$start = $_GET['start'];

//OBTENEMOS EL TOTAL DE REGISTROS
$countSql = "SELECT count(v.id) as Total $from WHERE $where";
$countSt = $pdo->query($countSql);
//echo $countSql;
$total = $countSt->fetch()['Total'];


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(v.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);
//echo $queryFiltered;

$resFilterLength = $pdo->query($queryFiltered);
$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','p.activo','p.id'];

//$sql2 = "SELECT SUM(CASE WHEN v.tipo_comprobante IN ('NCA','NCB') THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
$sql2 = "SELECT SUM(CASE WHEN v.facturacion='nota_credito' THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();

$total_facturas_recibos = ($row2['total_facturas_recibos'] ?: 0);

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//echo $sql;
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
    foreach ($pdo->query($sql) as $row) {

      //['v.id','date_format(v.fecha_hora,"%d/%m/%Y %H:%i")','v.facturacion','c.razon_social','v.total'];//,'v.estado'

      $aProductos[]=[
        "id_venta"=>$row['id'],
        "fecha_venta"=>$row[1],// AS fecha_hora
        //"tipo_comprobante"=>$tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]),
        "facturacion"=>$row["facturacion"],
        "modalidad_pago"=>$row["modalidad_pago"],
        "cliente"=>$row['razon_social'],
        "total"=>$row['total'],
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
      'total_facturas_recibos'=>$total_facturas_recibos,
    ];
} else {
    var_dump($pdo->errorInfo());
    die;
}

echo json_encode([
  'data' => $aProductos,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aProductos),
  'queryInfo'=>$queryInfo,
]);