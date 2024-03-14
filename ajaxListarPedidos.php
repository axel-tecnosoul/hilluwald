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

//$data_columns = ["","p.cb","p.codigo","c.categoria","p.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","p.precio","p.activo"];//PARA EL ORDENAMIENTO

$data_columns = $fields = ['p.id','date_format(p.fecha,"%d/%m/%Y")','c.razon_social','p.campana','p.pago_completo','p.despacho_completo',"GROUP_CONCAT('+',FORMAT(pd.cantidad_plantines,0,'de_DE'),' ',e.especie SEPARATOR '<br>') AS detalle_cultivos"];//,'p.estado'

//$from="FROM pedidos p INNER JOIN pedidos_detalle pd ON pd.id_pedido=p.id LEFT JOIN clientes c ON c.id = p.id_cliente INNER JOIN cultivos cu ON pd.id_cultivo=cu.id";
$from="FROM pedidos p INNER JOIN pedidos_detalle pd ON pd.id_pedido=p.id LEFT JOIN clientes c ON c.id = p.id_cliente INNER JOIN especies e ON pd.id_cultivo=e.id";

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

/*$tipo_comprobante=$_GET["tipo_comprobante"];
$filtroTipoComprobante="";
if($tipo_comprobante!=""){
  $ex=explode(",",$tipo_comprobante);
  $tipo_comprobante="'".implode("','",$ex)."'";
  $filtroTipoComprobante=" AND p.facturacion IN ($tipo_comprobante)";
}*/

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "p.anulado = 0";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_sucursal']; 
}
$group_by=" GROUP BY p.id";
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
$countSql = "SELECT count(p.id) as Total $from WHERE $where";
$countSt = $pdo->query($countSql);
//echo $countSql;
$total = $countSt->fetch()['Total'];


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(p.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);
//echo $queryFiltered;

$resFilterLength = $pdo->query($queryFiltered);
$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','p.activo','p.id'];

//$sql2 = "SELECT SUM(CASE WHEN p.tipo_comprobante IN ('NCA','NCB') THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
/*$sql2 = "SELECT SUM(CASE WHEN p.facturacion='nota_credito' THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
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

      //['p.id','date_format(p.fecha_hora,"%d/%m/%Y %H:%i")','p.facturacion','c.razon_social','p.total'];//,'p.estado'

      $pago_completo="No";
      if($row['pago_completo']==1){
        $pago_completo="Si";
      }
      $despacho_completo="No";
      if($row['despacho_completo']==1){
        $despacho_completo="Si";
      }

      $aPedidos[]=[
        "id_pedido"=>$row['id'],
        "fecha"=>$row[1],// AS fecha_hora
        //"tipo_comprobante"=>$tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]),
        "razon_social"=>$row["razon_social"],
        "campana"=>$row["campana"],
        "pago_completo"=>$pago_completo,
        "despacho_completo"=>$despacho_completo,
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
      //'total_facturas_recibos'=>$total_facturas_recibos,
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
]);