<?php
require('vendor/fpdf/fpdf.php');
include_once('database.php');
$id = $_GET['id'];
class PDF extends FPDF{
  function recibo($emision) {
    global $id;
    $x2 = 55;
    $x = 2;
    if($emision == "DUPLICADO"){
      /*$x+=148;
      $x2+=148;*/
    }
    $this->AliasNbPages(); //muestra la pagina / y total de paginas
    $this->SetDrawColor(0, 0, 0); //colorBorde
    $this->SetAutoPageBreak(false);
    // Cabecera de página
    //function Header(){
    //$id = 5;
      
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT pd.id,date_format(pd.fecha,'%d/%m/%Y') as fecha, c.razon_social as cliente, c.cuit, c.direccion, su.nombre as sucursal, pd.campana, pd.observaciones, pd.pago_completo, pd.despacho_completo, pd.motivo_saldado, pd.id_usuario, pd.fecha_hora_alta FROM pedidos pd INNER JOIN pedidos_detalle pde ON pde.id_pedido= pd.id INNER JOIN clientes c ON pd.id_cliente = c.id LEFT JOIN sucursales su ON pd.id_sucursal = su.id WHERE pd.id = ?";
    //echo $sql;
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
    $data = $q->fetch(PDO::FETCH_ASSOC);
    $cuitCliente="-";
    if($data["cuit"]){
      $cuitCliente=$data["cuit"];
    }

    /* Variables*/
    $punto_venta = "4";
    $nombre = $data['cliente'];
    $cuit = $data['cuit'];
    $fecha_inicio_actividad = "13/03/2023";
    $fecha_vto_pago = "05/04/2023";
    $ingresos_brutos = "20-34149121-6";
    
    $obs="";
    if(strlen($obs)>100){
      $obs=substr($obs,0,100)."[...]";
    }
    /* LINEAS HORIZONTALES*/
    //$this->SetDrawColor(255, 0, 0, 0);
    $this->Line($x, 4, 91 + $x2,4);
    //$this->SetDrawColor(0, 255, 0, 0);
    $this->Line($x, 12, 91 + $x2,12);
    //$this->SetDrawColor(0, 0, 0, 255);
    //$this->Line( 12+ $x2, 30,28 + $x2,30);//linea del cuadrado que esta arriba en el centro
    //$this->SetDrawColor(255, 0, 255, 0);
    $this->Line($x, 42, 91 + $x2,42);
    //Division//
    
    //$this->SetDrawColor(0, 0, 255, 0);
    $this->Line($x, 55, 91 + $x2,55);
    //$this->SetDrawColor(0, 255, 0, 0);
    $this->Line($x, 63, 91 + $x2,63);
    //$this->SetDrawColor(0, 0, 0, 255);
    //$this->Line($x, 77,91 + $x2,77);
    //$this->SetDrawColor(255, 0, 0, 0);
    
    /* LINEAS VERTICALES*/
    //$this->SetDrawColor(255, 0, 0, 255);
    $this->Line($x, 4,$x,63);
    //$this->SetDrawColor(0, 0, 255, 255);
    //$this->Line( 12+ $x2, 14, 12+ $x2,30);//linea derecha del cuadro superior que divide la cabecera
    //$this->SetDrawColor(255, 0, 255, 0);
    //$this->Line(28 + $x2, 14,28 + $x2,30);//linea derecha del cuadro superior que divide la cabecera
    //$this->SetDrawColor(255, 0, 0, 0);
    //$this->Line( 20+ $x2, 30, 20+ $x2,50);//linea que divide la cabecera del remito y esta por debajo del cuadrado
    //$this->SetDrawColor(0, 255, 0, 0);
    $this->Line(91 + $x2, 4, 91 + $x2,63);
    //$this->SetDrawColor(0, 0, 0, 255);
    //$this->Line(91 + $x2, 68, 91 + $x2,770);
    //$this->SetDrawColor(0, 0, 0, 0);
    //$this->SetDrawColor(255, 0, 0, 0);
    //$this->Line($x, 68,$x,77);

    // ORIGINAL
    //$this->getY(). ' ' . $this->getX(). ' ' .
    $this->Cell(4+ $x2);  // mover a la derecha
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(144, -4, utf8_decode(($emision == "DUPLICADO") ? "DUPLICADO" : "ORIGINAL"),0,0,'L');
    $this->Ln(5);

    // Filas 
    //$this->Image('img/logoMaury2.png',0 + $x,13,36);
    $this->Image('img/logo horizontal.jpeg',5 + $x,22,26);
    $this->Ln(6);

    // Domicilio 
    //$this->Cell(30);  // Margen izquierdo
    $this->Cell(-25 + $x2);
    //$this->Cell(30 + $x2);  // Margen izquierdo
    //$this->Cell(-63 + $x2);  // mover a la derecha
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(15, 4, utf8_decode("Direccion: "), 0, 0, 'L', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(35, 4, utf8_decode("San Miguel y 20 de Junio"), 0, 0, '', 0);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(56, 0, utf8_decode('Orden de pedido'),0,0,'C');
    $this->Ln(4);

    // Correo 
    //$this->Cell(30);  // Margen izquierdo
    $this->Cell(-25 + $x2);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(6, 4, utf8_decode("CP: "), 0, 0, 'L', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(40, 4, utf8_decode("3334 - Puerto Rico - Misiones"), 0, 0, '', 0);
    $this->SetFont('Arial', '', 12);
    $this->Cell(32, 4, utf8_decode("Nº: "), 0, 0, 'R', 0);
    $this->SetFont('Arial', '', 12);
    $this->Cell(28, 4, utf8_decode($data["id"]), 0, 0, 'L', 0);
    $this->Ln();
    
    // TEL 
    //$this->Cell(30);  // Margen izquierdo
    $this->Cell(-25 + $x2);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(6, 4, utf8_decode("Tel: "), 0, 0, 'L', 0);
    $this->SetFont('Arial', '', 8);
    //$this->Cell(0, 18, utf8_decode("Monotributista"), 0, 0, '', 0);
    $this->Cell(30, 4, utf8_decode("(03743) 15443250"), 0, 0, '', 0);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(46, 10, utf8_decode("Fecha de Emisión: "), 0, 0, 'R', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(22, 10, utf8_decode($data["fecha"]), 0, 0, 'L', 0);
    
    $this->Ln(15);

    // Apellido y Nombre / Razon Social 
    $this->Cell(-63 + $x2);  // mover a la derecha
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(20, 4, utf8_decode("Razon Social: "), 0, 0, '', 0);
    //$this->Ln(0);
    //$this->Cell(-29+ $x2);
    $this->SetFont('Arial', '', 8);
    $this->Cell(70, 4, utf8_decode($data['cliente']), 0, 0, '', 0);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(10, 4, utf8_decode("CUIT: "), 0, 0, '', 0);
    //$this->Ln(0);
    //$this->Cell(27+ $x2);
    $this->SetFont('Arial', '', 8);
    $this->Cell(10, 4, utf8_decode($cuitCliente), 0, 0, '', 0);
    $this->Ln(5);

    // Condición IVA 
    $this->Cell(-63 + $x2);  // mover a la derecha
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(30, 4, utf8_decode("Domicilio Comercial: "), 0, 0, '', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(100, 4, utf8_decode($data['direccion']), 0, 0, '', 0);
    /*$this->SetFont('Arial', 'B', 8);
    $this->Cell(30, 4, utf8_decode("Condición IVA: "), 0, 0, '', 0);
    //$this->Ln(0);
    //$this->Cell(42+ $x2);
    $this->SetFont('Arial', '', 8);
    $this->Cell(25, 4, utf8_decode("Consumidor Final"), 0, 0, '', 0);*/
    $this->Ln(13);

    // Condicion Venta 
    $this->Cell( -63 + $x2);  // mover a la derecha
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(28, -6, utf8_decode("Condición de Venta: "), 0, 0, '', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(0, -6, utf8_decode('Cuenta Corriente'), 0, 0, '', 0);
    $this->Ln(4);

    // CAMPOS DE LA TABLA 
    $this->Cell(-63 + $x2);
    $this->SetFillColor(160, 160, 160); //colorFondo
    $this->SetTextColor(0, 0, 0); //colorTexto
    $this->SetDrawColor(0, 0, 0); //colorBorde
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(15, 7, utf8_decode('Cantidad'), 1, 0, 'C', 1);
    $this->Cell(85, 7, utf8_decode('Descripción'), 1, 0, 'C', 1);
    $this->Cell(22, 7, utf8_decode('Precio Unit.'), 1, 0, 'C', 1);
    $this->Cell(22, 7, utf8_decode('Importe'), 1, 1, 'C', 1);

    $this->SetFillColor(255, 255, 255); //colorFondo
    $this->SetTextColor(0, 0, 0); //colorTexto
    $this->SetDrawColor(0, 0, 0); //colorBorde
    $this->SetFont('Arial', '', 8);

    $sql2 = "SELECT cu.material, se.servicio, es.especie, cu.material, pr.procedencia, pde.cantidad_plantines, pde.plantines_pagados, pde.plantines_retirados FROM pedidos_detalle pde INNER JOIN pedidos pd ON pd.id = pde.id_pedido LEFT JOIN cultivos cu ON pde.id_material = cu.id INNER JOIN servicios se ON pde.id_servicio = se.id INNER JOIN especies es ON pde.id_especie = es.id LEFT JOIN procedencias_especies pr ON pde.id_procedencia = pr.id WHERE pde.id_pedido = $id ";
    $total = 0;
    $ln = 0;
    
    foreach ($pdo->query($sql2) as $row){
      $descripcion=$row["especie"];
      if(strlen($descripcion)>105){
          $descripcion=substr($descripcion,0,105)."[...]";
      }
      $subtotal=$row["cantidad_plantines"];
      $this->Cell(-63 + $x2);
      $this->Cell(15, 7, utf8_decode($row["cantidad_plantines"]), 1, 0, 'C', 0);
      $this->Cell(85, 7, utf8_decode($descripcion), 1, 0, 'L', 0);
      $this->Cell(22, 7, utf8_decode("$".number_format(/*$row[""]*/ 2000, 2,',', '.')), 1, 0, 'R', 0);
      $this->Cell(22, 7, utf8_decode("$".number_format(/*$row[""]*/ 2000, 2,',', '.')), 1, 1, 'R', 0);
      $total= $subtotal * 2000;
      $ln = $ln + 7;//Salto de linea que resta del total
    }

    //$ln = 99 - $ln;
    $ln = 111 - $ln;
    $this->Ln($ln);//Con 1 solo dato en la tabla el valor seria 144
    $this->Cell(30+ $x2);
    $this->SetFillColor(160, 160, 160); //colorFondo
    $this->SetTextColor(0, 0, 0); //colorTexto
    $this->SetDrawColor(0, 0, 0); //colorBorde
      
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(51, 8, utf8_decode("Total Venta"), 0, 0, '', 1);
    $this->Ln(0);
    $this->Cell(65+ $x2);
    $this->Cell(16, 8, utf8_decode("$".number_format($total,2, ',', '.')), 0, 0, 'R', 0);
    /**************************************************************************************/
    /* Lineas Horizontales */
    //$this->SetDrawColor(255, 0, 0, 0);
    $this->Line(40 + $x2, 184,91 + $x2,184);
    //$this->SetDrawColor(0, 0, 255, 0);
    $this->Line(40 + $x2, 192,91 + $x2,192);
    /* Lineas Verticales */
    //$this->SetDrawColor(0, 255, 0, 0);
    $this->Line(40 + $x2, 184,40 + $x2,192);
    //$this->SetDrawColor(0, 0, 0, 255);
    $this->Line(91 + $x2, 184,91 + $x2,192);
    /**********************************************************/
    $this->Ln(10);
    $this->Cell(-63 + $x2);
    $this->SetFont('Arial', 'B', 8);
    //$this->Cell(89+ $x2, 8, utf8_decode("Observaciones: "), 1, 0, '', 0);
    $this->Cell(144, 8, utf8_decode("Observaciones: "), 1, 0, '', 0);
    $this->Ln(0);
    $this->Cell(-41+ $x2);
    $this->SetFont('Arial', '', 8);
    $this->Cell(100, 8, utf8_decode($obs), 0, 0, '', 0);

    $this->Ln(8);
    $this->Cell(-63 + $x2);
    //$this->SetY(-125+ $x2); // Posición: a 1,5 cm del final
    $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
    //$this->Cell(274, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)
    $this->Cell(274, 10, utf8_decode(''), 0, 0, 'C'); //pie de pagina(numero de pagina)
    $this->Ln(0);
    $this->Cell(-63 + $x2);
    //$this->SetY(-125+ $x2); // Posición: a 1,5 cm del final
    $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
    $this->Cell(144, 8,utf8_decode('2024 @ Desarrollado por Misiones Software'), 0, 0, 'C'); // pie de pagina(fecha de pagina)
  }
}


//$pdf = new PDF('L', 'mm','A4');
//$pdf = new PDF('L', 'mm',array(210,297));
//$pdf = new PDF('P', 'mm',array(148,210));
//$pdf = new PDF('P', 'mm',array(210,148));
$pdf = new PDF('P', 'mm','A5');
//$pdf->AddPage(); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */

//$pdf->AliasNbPages(); //muestra la pagina / y total de paginas
//$pdf->SetFont('Arial', 'B', 8);
//$pdf->SetDrawColor(0, 0, 0); //colorBorde
$pdf->addPage();
$pdf->recibo("ORIGINAL");
$pdf->AddPage();
//$pdf->setY(10);
$pdf->recibo("DUPLICADO");
/*$i = 0;

$i = $i + 1;*/
/* TABLA */



$pdf->Output('recibo.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)