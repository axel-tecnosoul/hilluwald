<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
include 'vendor/afip/Afip.php';
include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.

//$_GET["id"]=52;

if ( !empty($_GET)) {

  $afip = new Afip($aInitializeAFIP);
  $server_status = (array) $afip->ElectronicBilling->GetServerStatus();
  //var_dump($server_status);
  //$server_status["AppServer"]="Error";
  //$server_status["DbServer"]="Error2";
  //$server_status["AuthServer"]="Error3";
  $afip_ok=0;
  foreach($server_status as $key => $value){
    if($key!="DbServer"){//este parametro no lo contemplamos porque casi siempre está en NO y funciona igual
      if($value!="OK"){
        if($afip_ok==0){
          echo "<br>AFIP informa los siguientes errores:<br>";
        }
        $afip_ok++;
        echo "<b>".$key.":</b> ".$value."<br>";
      }
    }
  }
  if($afip_ok>0){
    die("Actualiza la página para volver a intentarlo o vuelve mas tarde.");
  }

  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;

  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_GET);
  }

  $idVenta=$_GET["id"];

  $sql = "UPDATE ventas SET anulada = 1 WHERE id = ?";
  $q = $pdo->prepare($sql);
  $params=array($idVenta);
  $q->execute($params);
  //$idVenta = $pdo->lastInsertId();

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
  
  //if($tipo_comprobante!="R"){
    $afip = new Afip($aInitializeAFIP);
    $server_status = $afip->ElectronicBilling->GetServerStatus();
    /*echo 'Este es el estado del servidor:';
    var_dump($server_status);*/

    $sql = "SELECT id_venta,fecha_cbte,tipo_doc,dni,tipo_comprobante,total_bruto,total_neto,id_iva,total_iva,alicuota_ingresos_brutos,total_ingresos_brutos,punto_venta,numero_cbte,estado,cae,fecha_vto_cae,id_cbte_relacionado FROM facturas WHERE id_venta = $idVenta ";
    foreach ($pdo->query($sql) as $row) {
      $aIva=[];
      if($row["tipo_comprobante"]=="A"){
        $tipo_comprobante_asociado=1;//1 -> Factura A
        $tipo_comprobante=3;//3 -> Nota de Crédito A
        $tipo_comprobante_bbdd="NCA";
        /*$DocTipo=80;
        $DocNro=$row["dni"];*/
    
        /*$ImpNeto=$ImpTotal/1.21;
        $ImpIVA=$ImpTotal-$ImpNeto;*/
        $porcentaje_ingresos_brutos=$row["alicuota_ingresos_brutos"];

        $sql = "SELECT iva,SUM(subtotal) AS monto FROM ventas_detalle WHERE id_venta = $idVenta GROUP BY iva ";
        foreach ($pdo->query($sql) as $row2) {
          $iva=$row2["iva"];
          $monto=$row2["monto"];
          $total_impuestos=$iva+$porcentaje_ingresos_brutos;

          $IdIVA=4;
          if($iva==21){
            $IdIVA=5;
          }
          
          $ImpNeto=$monto/(($total_impuestos/100)+1);//1.21
          $ImpIVA=$ImpNeto*($iva/100);
  
          $ImpNeto=number_format($ImpNeto,2,".","");
          $ImpIVA=number_format($ImpIVA,2,".","");
  
          $aIva[]=array(
            'Id'=> $IdIVA, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
            'BaseImp'=> $ImpNeto,//100, // Base imponible -> ES IGUAL A ImpNeto?
            'Importe'=> $ImpIVA,//21 // Importe -> ES IGUAL A ImpIVA?
          );
        }
      }elseif($row["tipo_comprobante"]=="B"){
        $tipo_comprobante_asociado=6;//6 -> Factura B
        $tipo_comprobante=8;//8 -> Nota de Crédito B
        $tipo_comprobante_bbdd="NCB";
        
        /*$ImpNeto=number_format($ImpNeto,2,".","");
        $ImpIVA=number_format($ImpIVA,2,".","");*/

        $aIva[]=array(
          'Id'=> $row["id_iva"], // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
          'BaseImp'=> $row["total_neto"],//100, // Base imponible -> ES IGUAL A ImpNeto?
          'Importe'=> $row["total_iva"],//21 // Importe -> ES IGUAL A ImpIVA?
        );
      }
      
      /*$sumaIVA=0;
      foreach ($factura as $iva => $monto) {

        $IdIVA=4;
        if($iva==21){
          $IdIVA=5;
        }
        //$ImpIVA=$monto*$iva/100;
        //$ImpNeto=$monto-$ImpIVA;
        $ImpNeto=$monto/(($iva/100)+1);//1.21
        $ImpIVA=$ImpNeto*($iva/100);

        if($tipo_comprobante==1){
          $ImpNeto=$monto/(($iva/100)+1);//1.21
        }
        $ImpNeto=number_format($ImpNeto,2,".","");
        $ImpIVA=number_format($ImpIVA,2,".","");

        $sumaIVA+=$ImpIVA;

        $aIva[]=array(
          'Id'=> $IdIVA, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
          'BaseImp'=> $ImpNeto,//100, // Base imponible -> ES IGUAL A ImpNeto?
          'Importe'=> $ImpIVA,//21 // Importe -> ES IGUAL A ImpIVA?
        );
      }
      $ImpIVA=$sumaIVA;
      $ImpNeto=$ImpTotal-$ImpIVA;*/
      
      $fecha=date('Y-m-d');
      $punto_venta=$row["punto_venta"];
      $DocTipo=$row["tipo_doc"];
      $DocNro=$row["dni"];
      $fecha=$row["fecha_cbte"];
      $ImpTotal=$row["total_bruto"];
      $ImpNeto=$row["total_neto"];
      $ImpIVA=$row["total_iva"];
      $CbteAsoc=$row["numero_cbte"];

      $data = array(
        'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
        'PtoVta' 	=> $punto_venta,  // Punto de venta
        'CbteTipo' 	=> $tipo_comprobante,  // Tipo de comprobante (ver tipos disponibles) 
        'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
        'DocTipo' 	=> $DocTipo, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles). Para comprobantes clase A y M el campo DocTipo debe ser igual a 80 (CUIT)
        'DocNro' 	=> $DocNro,  // Número de documento del comprador (0 consumidor final)
        'CbteDesde' 	=> 2,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
        'CbteHasta' 	=> 2,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
        'CbteFch' 	=> intval(date('Ymd',strtotime($fecha))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
        'ImpTotal' 	=> $ImpTotal,//121, // Importe total del comprobante
        'ImpTotConc' 	=> 0,   // Importe neto no gravado
        'ImpNeto' 	=> $ImpNeto,//100, // Importe neto gravado
        'ImpOpEx' 	=> 0,   // Importe exento de IVA
        'ImpIVA' 	=> $ImpIVA,//21,  //Importe total de IVA
        'ImpTrib' 	=> $row["total_ingresos_brutos"],   //Importe total de tributos
        'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
        'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
        'Iva'=>$aIva, // (Opcional) Alícuotas asociadas al comprobante
        'Tributos' 		=> array( // (Opcional) Tributos asociados al comprobante
          array(
            'Id' 		=>  2, // Id del tipo de tributo (ver tipos disponibles) 
            'Desc' 		=> 'Ingresos Brutos', // (Opcional) Descripcion
            'BaseImp' 	=> number_format($ImpNeto,2,".",""), // Base imponible para el tributo
            'Alic' 		=> $row["alicuota_ingresos_brutos"], // Alícuota
            'Importe' 	=> number_format($row["total_ingresos_brutos"],2,".","") // Importe del tributo
          )
        ),
        'CbtesAsoc' 		=> array( // (Solo para notas de credito o debito) Array de comprobantes asociados
          array(
            'Tipo' 		=> $tipo_comprobante_asociado, // tipo de factura
            'PtoVta' 	=> $punto_venta,//punto de venta de la factura
            'Nro' 	=> $CbteAsoc,//nro de comprobante de la factura
          )
        ),
      );

      if ($modoDebug==1) {
        var_dump($data);
      }
      
      //$res = $afip->ElectronicBilling->CreateVoucher($data);
      $res = $afip->ElectronicBilling->CreateNextVoucher($data);

      $estado="E";
      if(isset($res['CAE'])){
        $estado="A";
        $CAE=$res['CAE'];//CAE asignado el comprobante
        $CAEFchVto=$res['CAEFchVto'];//Fecha de vencimiento del CAE (yyyy-mm-dd)
        $voucher_number=$res['voucher_number'];//Número asignado al comprobante
        //var_dump($res);
      }
      
      if ($modoDebug==1) {
        var_dump($res);
        var_dump($CAE);
        var_dump($CAEFchVto);
        var_dump($voucher_number);
      }

      $sql = "INSERT INTO facturas (id_venta,fecha_cbte,tipo_doc,dni,tipo_comprobante,total_bruto,total_neto,id_iva,total_iva,alicuota_ingresos_brutos,total_ingresos_brutos,punto_venta,numero_cbte,estado,cae,fecha_vto_cae,id_cbte_relacionado) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NULL)";
      $q = $pdo->prepare($sql);
      $params=array($idVenta,$fecha,$DocTipo,$DocNro,$tipo_comprobante_bbdd,$ImpTotal,$ImpNeto,$row["id_iva"],$ImpIVA,$row["alicuota_ingresos_brutos"],$row["total_ingresos_brutos"],$punto_venta,$voucher_number,$estado,$CAE,$CAEFchVto);
      $q->execute($params);
      $afe=$q->rowCount();

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
    }

  if($afe!=1){
    var_dump($aDebug);
    die();
  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }
  Database::disconnect();

/*

  //obtenemos los datos de la factura para insertarlos en la nota de credito
  $sql = "SELECT id_cliente, total, punto_venta, numero_comprobante, tipo_comprobante, fecha_hora FROM facturas WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  //guardamos la nota de credito con los mismos datos de la factura y establemecemos el id_venta_cbte_relacionado
  $sql = "INSERT INTO ventas (fecha_hora, id_cliente, total, id_usuario, id_venta_cbte_relacionado) VALUES (now(),?,?,?,?,?,?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($data["id_cliente"],$data["total"],$_SESSION['user']['id'],$id));
  $idVentaCbteRelacionado = $pdo->lastInsertId();

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

  //actualizamos la venta para informar que la factura tiene una nota de credito relacionada
  $sql = "UPDATE ventas set id_venta_cbte_relacionado = ? where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($idVentaCbteRelacionado,$id));

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

  //seleccionamos todos los productos de la factura
  $sql = " SELECT vd.id_producto, vd.cantidad, vd.precio, vd.subtotal, vd.id_modalidad, vd.deuda_proveedor, vd.pagado, p.id_proveedor, v.id_almacen from ventas_detalle vd inner join ventas v on v.id = vd.id_venta inner join productos p on p.id = vd.id_producto where vd.id_venta = ".$id;
	foreach ($pdo->query($sql) as $row) {
    //replicamos el detalle de los productos de la factura a la nota de credito
    $sqlA = "INSERT INTO ventas_detalle (id_venta, id_producto, cantidad, precio, subtotal, id_modalidad, deuda_proveedor, pagado) VALUES (?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sqlA);
    $q->execute(array($idVentaCbteRelacionado,$row["id_producto"],$row["cantidad"],$row["precio"],$row["subtotal"],$row["id_modalidad"],$row["deuda_proveedor"],$row["pagado"]));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }
	
	}
  
  include './../external/afip/Afip.php';
  
  include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.
  $afip = new Afip($aInitializeAFIP);

  $punto_venta=$data["punto_venta"];

  $server_status = $afip->ElectronicBilling->GetServerStatus();
  //echo 'Este es el estado del servidor:';
  //var_dump($server_status);

  $ImpTotal=$data["total_con_descuento"];
  $CbteAsoc=$data["numero_comprobante"];
  //$total=121;
  if($data["tipo_comprobante"]=="A"){
    $tipo_comprobante_asociado=1;//1 -> Factura A
    $tipo_comprobante=3;//3 -> Nota de Crédito A
    $tipo_de_nota="NCA";
    $DocTipo=80;
    $DocNro=$_POST["dni"];

    $ImpNeto=$ImpTotal/1.21;
    $ImpIVA=$ImpTotal-$ImpNeto;
  }elseif($data["tipo_comprobante"]=="B"){
    $tipo_comprobante_asociado=6;//6 -> Factura B
    $tipo_comprobante=8;//8 -> Nota de Crédito B
    $tipo_de_nota="NCB";
    $DocTipo=99;
    $DocNro=0;
    
    $ImpNeto=$ImpTotal/1.21;
    $ImpIVA=$ImpTotal-$ImpNeto;
  }
  $ImpNeto=number_format($ImpNeto,2,".","");
  $ImpIVA=number_format($ImpIVA,2,".","");
  

  $dataNC = array(
    'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
    'PtoVta' 	=> $punto_venta,  // Punto de venta
    'CbteTipo' 	=> $tipo_comprobante,  // Tipo de comprobante (ver tipos disponibles) 
    'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
    'DocTipo' 	=> $DocTipo, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles). Para comprobantes clase A y M el campo DocTipo debe ser igual a 80 (CUIT)
    'DocNro' 	=> $DocNro,  // Número de documento del comprador (0 consumidor final)
    'CbteDesde' 	=> 2,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
    'CbteHasta' 	=> 2,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
    'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
    'ImpTotal' 	=> $ImpTotal,//121, // Importe total del comprobante
    'ImpTotConc' 	=> 0,   // Importe neto no gravado
    'ImpNeto' 	=> $ImpNeto,//100, // Importe neto gravado
    'ImpOpEx' 	=> 0,   // Importe exento de IVA
    'ImpIVA' 	=> $ImpIVA,//21,  //Importe total de IVA
    'ImpTrib' 	=> 0,   //Importe total de tributos
    'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
    'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
    'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
      array(
        'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
        'BaseImp' 	=> $ImpNeto,//100, // Base imponible -> ES IGUAL A ImpNeto?
        'Importe' 	=> $ImpIVA,//21 // Importe -> ES IGUAL A ImpIVA?
      )
    ), 
    'CbtesAsoc' 		=> array( // (Solo para notas de credito o debito) Array de comprobantes asociados
      array(
        'Tipo' 		=> $tipo_comprobante_asociado, // tipo de factura
        'PtoVta' 	=> $punto_venta,//punto de venta de la factura
        'Nro' 	=> $CbteAsoc,//nro de comprobante de la factura
      )
    ), 
  );

  //var_dump($dataNC);
  
  //$res = $afip->ElectronicBilling->CreateVoucher($dataNC);
  $res = $afip->ElectronicBilling->CreateNextVoucher($dataNC);
  
  $estado="E";
  if(isset($res['CAE'])){
    $estado="A";
    $CAE=$res['CAE'];//CAE asignado el comprobante
    $CAEFchVto=$res['CAEFchVto'];//Fecha de vencimiento del CAE (yyyy-mm-dd)
    $voucher_number=$res['voucher_number'];//Número asignado al comprobante
    //var_dump($res);
  }
  
  if ($modoDebug==1) {
    var_dump($res);
    var_dump($CAE);
    var_dump($CAEFchVto);
    var_dump($voucher_number);
  }

  //actualizamos la nota de credito con los datos devueltos por AFIP
  $sql = "UPDATE ventas SET tipo_comprobante = ?, tipo_doc = ?, estado = ?, punto_venta = ?, numero_comprobante = ?, cae = ?, fecha_vencimiento_cae = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($tipo_de_nota,$DocTipo,$estado,$punto_venta,$voucher_number,$CAE,$CAEFchVto,$idVentaCbteRelacionado));

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }

  Database::disconnect();*/
  
}

header("Location: listarVentas.php");