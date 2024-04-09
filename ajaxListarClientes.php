<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php';
include 'funciones.php';
session_start();
$aClientes=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

//$data_columns = ["","c.cb","c.codigo","c.categoria","c.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","c.precio","c.activo"];//PARA EL ORDENAMIENTO

$data_columns = $fields = ['c.id','c.razon_social','c.cuit','c.cond_fiscal','c.direccion','c.email', 'c.telefono', 'c.notas', 'l.localidad', 'c.activo'];

$from="FROM clientes c LEFT JOIN cliente_sucursal cs ON cs.id_cliente=c.id LEFT JOIN localidades l ON l.id = c.id_localidad";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

// $desde=$_GET["desde"];
// $filtroDesde="";
// if($desde!=""){
//   $filtroDesde=" AND DATE(c.fecha)>='$desde'";
// }

// $hasta=$_GET["hasta"];
// $filtroHasta="";
// if($hasta!=""){
//   $filtroHasta=" AND DATE(c.fecha)<='$hasta'";
// }

// $id_cliente_sucursal=$_GET["id_cliente_sucursal"];
// $filtroCliente="";
// if($id_cliente_sucursal!=0 and $id_cliente_sucursal!=""){
//   $filtroCliente=" AND cd.id_cliente_sucursal IN ($id_cliente_sucursal)";
// }

/*$tipo_comprobante=$_GET["tipo_comprobante"];
$filtroTipoComprobante="";
if($tipo_comprobante!=""){
  $ex=explode(",",$tipo_comprobante);
  $tipo_comprobante="'".implode("','",$ex)."'";
  $filtroTipoComprobante=" AND c.facturacion IN ($tipo_comprobante)";
}*/

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "c.activo = 1";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_sucursal']; 
}
$group_by=" GROUP BY c.id";
$whereFiltered=$where;//.$filtroTipoComprobante;

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
$countSql = "SELECT count(c.id) as Total $from WHERE $where";
$countSt = $pdo->query($countSql);
//echo $countSql;
//$total = $countSt->fetch()['Total'];
$total=0;
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
$queryFiltered="SELECT COUNT(c.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
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
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','c.activo','c.id'];

//$sql2 = "SELECT SUM(CASE WHEN c.tipo_comprobante IN ('NCA','NCB') THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
/*$sql2 = "SELECT SUM(CASE WHEN c.facturacion='nota_credito' THEN total*-1 ELSE total END) AS total_facturas_recibos $from WHERE $whereFiltered ";
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
      if($row["activo"] == 1){
        $activo = 'Si';
      }else{
        $activo = 'No';
      }
      $aClientes[]=[
        "id_cliente"=>$row['id'],
        "razon_social"=>$row["razon_social"],
        "cond_fiscal"=>$row["cond_fiscal"],
        "localidad"=>$row["localidad"],
        "activo"=>$activo,
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
  'data' => $aClientes,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aClientes),
  'queryInfo'=>$queryInfo,
]);