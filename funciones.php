<?php
function get_nombre_comprobante($tipo_comprobante){
  switch ($tipo_comprobante) {
    case 'R':
      $tipo_cbte="Recibo";
      break;
    case 'A':
      $tipo_cbte="Factura A";
      break;
    case 'B':
      $tipo_cbte="Factura B";
      break;
    case 'NCA':
      $tipo_cbte="Nota de Crédito A";
      break;
    case 'NCB':
      $tipo_cbte="Nota de Crédito B";
      break;
    default:
      $tipo_cbte="";
      break;
  }
  return $tipo_cbte;
}

function get_estado_comprobante($estado_abreviado){
  switch ($estado_abreviado) {
    case 'R':
      $estado_completo="Rechazado";
      break;
    case 'A':
      $estado_completo="Aprobado";
      break;
    case 'E':
      $estado_completo="ERROR";
      break;
    default:
      $estado_completo="";
      break;
  }
  return $estado_completo;
}

function format_numero_comprobante($punto_venta,$numero_comprobante){
  return str_pad($punto_venta,4,"0",STR_PAD_LEFT)."-".str_pad($numero_comprobante,8,"0",STR_PAD_LEFT);
}

function acortarPalabras($texto) {
  // Convertimos el texto en un array de palabras
  $palabras = explode(" ", $texto);
  
  // Creamos un array para almacenar las palabras acortadas
  $palabrasAcortadas = array();

  // Recorremos cada palabra y la acortamos si tiene más de dos letras
  foreach ($palabras as $palabra) {
      if (strlen($palabra) > 2) {
          // Obtenemos la primera letra de la palabra y la agregamos al array de palabras acortadas
          $palabrasAcortadas[] = $palabra[0];
      } else {
          // Si la palabra tiene dos letras o menos, la agregamos sin modificar al array de palabras acortadas
          $palabrasAcortadas[] = $palabra;
      }
  }
  
  // Unimos las palabras acortadas en una sola cadena separada por espacios
  return implode("", $palabrasAcortadas);
}

function insertPedido($id_cliente,$fecha,$campana,$sucursal,$observaciones,$id_despacho,$aDetallePedido){
  global $pdo,$modoDebug;
  
  $sql = "INSERT INTO pedidos (id_cliente, fecha, campana, id_sucursal, observaciones, id_despacho, id_usuario) VALUES (?,?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $params=array($id_cliente, $fecha, $campana, $sucursal, $observaciones, $id_despacho, $_SESSION['user']['id']);
  $q->execute($params);
  $id_pedido = $pdo->lastInsertId();

  $aDebug[]=[
    "consulta"=>$sql,
    "params"=>$params,
    "afe"=>$q->rowCount(),
  ];

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }
  
  //$cantPrendas = count($_POST["id_cultivo"]);

  $datosAgrupados = [];

  // Iteramos sobre los datos recibidos
  foreach ($aDetallePedido as $indice => $detalle) {
    
    // Creamos una clave única para representar la combinación de servicio, especie, procedencia y material
    $clave = $detalle['id_servicio'] . '_' . $detalle['id_especie'] . '_' . $detalle['id_procedencia'] . '_' . $detalle['id_material'];
    
    // Si la clave aún no existe en el array de datos agrupados, la creamos
    if (!array_key_exists($clave, $datosAgrupados)) {
      $datosAgrupados[$clave] = [
        'id_servicio' => $detalle['id_servicio'],
        'id_especie' => $detalle['id_especie'],
        'id_procedencia' => $detalle['id_procedencia'],
        'id_material' => $detalle['id_material'],
        'cantidad' => 0,
        'plantines_retirados' => 0,
      ];
    }
    
    // Sumamos la cantidad correspondiente a la clave
    $datosAgrupados[$clave]['cantidad'] += intval($detalle['cantidad']);
    $datosAgrupados[$clave]['plantines_retirados'] += intval($detalle['plantines_retirados']);
  }

  var_dump($datosAgrupados);
  

  $aProductos=[];

  $cantProdOK=0;
  foreach ($datosAgrupados as $key => $detalle) {

    $id_cultivo=NULL;

    $cantidad = $detalle['cantidad'];
    $id_servicio = $detalle['id_servicio'];
    $id_especie = $detalle['id_especie'];
    $id_procedencia = $detalle['id_procedencia'];
    $id_material = $detalle['id_material'];
    $plantines_retirados = $detalle['plantines_retirados'];

    if($id_procedencia<1) $id_procedencia=NULL;
    if($id_material<1) $id_material=NULL;

    if($cantidad>0){

      $aProductos[]=[
        "id_servicio"=>$id_servicio,
        "id_especie"=>$id_especie,
        "id_procedencia"=>$id_procedencia,
        "id_material"=>$id_material,
        "cantidad"=>$cantidad,
      ];

      $sql = "INSERT INTO pedidos_detalle (id_pedido, id_servicio, id_cultivo, id_especie, id_procedencia, id_material, cantidad_plantines, plantines_retirados) VALUES (?,?,?,?,?,?,?,?)";
      $q = $pdo->prepare($sql);
      //$q->execute(array($idVenta,$id_cultivo,$cantidad,$precio,$subtotal,$modalidad,$pagado));
      $params=array($id_pedido,$id_servicio,$id_cultivo,$id_especie,$id_procedencia,$id_material,$cantidad,$plantines_retirados);
      $result = $q->execute($params);
      //var_dump($result);
      
      if ($result === false) {
        // Si ocurrió un error, obtener información sobre el error
        $errorInfo = $q->errorInfo();
        // El mensaje de error se encuentra en el índice 2 del array
        $errorMessage = $errorInfo[2];
        // Mostrar el mensaje de error o realizar alguna otra acción
        echo "Error al ejecutar la consulta: $errorMessage";
      } else {
        // La consulta se ejecutó correctamente
        $afe = $q->rowCount();
      }

      if($afe==1){
        $cantProdOK++;
      }

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      $aDebug[]=[
        "consulta"=>$sql,
        "params"=>$params,
        "afe"=>$q->rowCount(),
      ];

    }

  }

  if ($modoDebug==1) {
    var_dump($aProductos);
    echo "cantProdOK!=count(aProductos)<br>";
    echo $cantProdOK."!=".count($aProductos)."<br>";
    var_dump($cantProdOK!=count($aProductos));
    echo "<br><br>";
  }

  $ok=1;
  if($cantProdOK!=count($aProductos)){
    $ok=0;
    var_dump($aDebug);
  }
  return $ok;
}