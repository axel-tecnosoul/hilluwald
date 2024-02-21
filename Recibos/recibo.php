<?php
require('../vendor/fpdf/fpdf.php');
include_once('../database.php');
class PDF extends FPDF{
      function recibo($emision) {
         $x2 = 55;
         $x = 2;
         if($emision == "DUPLICADO"){
            $x+=148;
            $x2+=148;
         }
         $this->AliasNbPages(); //muestra la pagina / y total de paginas
         $this->SetDrawColor(0, 0, 0); //colorBorde
         $this->SetAutoPageBreak(false);
      // Cabecera de página
      //function Header(){
         $id = 5;
         
         $pdo = Database::connect();
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $sql = "SELECT * FROM ventas v INNER JOIN clientes c ON v.id_cliente=c.id WHERE v.id = ?";
         $q = $pdo->prepare($sql);
         $q->execute(array($id));
         $data = $q->fetch(PDO::FETCH_ASSOC);

         /* Variables*/
         $punto_venta = "4";
         $cuit = "27-27032771-6";
         $fecha_inicio_actividad = "01/09/2017";
         $fecha_vto_pago = "05/04/2023";
         $ingresos_brutos = "27-27032771-6";
         $observaciones = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas tristique vel dui sed suscipit. Vivamus interdum tempor elit, et finibus mi euismod sed. Donec varius ex eu mattis fringilla. Integer interdum arcu ut magna consectetur molestie. Nunc sit amet purus sed felis aliquet facilisis. Phasellus eu lorem sit amet tellus tincidunt sollicitudin non ut ex. Cras ut tincidunt nisi. Donec in facilisis lorem, ac sagittis ex. Vestibulum vitae pretium dui. Nulla facilisi.

         Morbi luctus tortor arcu, ac dapibus sapien pharetra fermentum. Nulla hendrerit sem id metus vulputate finibus. Cras venenatis elementum felis, sit amet tristique turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse non urna vitae purus convallis convallis. Etiam scelerisque a orci quis sagittis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam malesuada, tortor ut vestibulum sodales, turpis ligula vehicula dolor, quis blandit turpis odio id massa. Sed hendrerit placerat eros sit amet pharetra. Maecenas lacinia ex id ante maximus, eu auctor magna dapibus. Aliquam luctus orci diam, ac aliquet sem aliquam eu.";
         $obs=$observaciones;
         if(strlen($obs)>100){
         $obs=substr($observaciones,0,100)."[...]";
         }
         /* LINEAS HORIZONTALES*/
         //$this->SetDrawColor(255, 0, 0, 0);
         $this->Line($x, 4,91 + $x2,4);
         //$this->SetDrawColor(0, 255, 0, 0);
         $this->Line($x, 14,91 + $x2,14);
         //$this->SetDrawColor(0, 0, 0, 255);
         $this->Line( 12+ $x2, 30,28 + $x2,30);
         //$this->SetDrawColor(255, 0, 255, 0);
         $this->Line($x, 50,91 + $x2,50);
         //Division//
         //$this->SetDrawColor(0, 0, 255, 0);
         $this->Line($x, 58,91 + $x2,58);
         //$this->SetDrawColor(0, 255, 0, 0);
         $this->Line($x, 59,91 + $x2,59);
         $this->SetDrawColor(0, 0, 0, 255);
         $this->Line($x, 77,91 + $x2,77);
         //$this->SetDrawColor(255, 0, 0, 0);
         $this->Line($x, 190,91 + $x2,190);
         //$this->SetDrawColor(0, 0, 0, 0);
         //$this->SetDrawColor(0, 0, 255, 0);
         $this->Line($x, 202,91 + $x2,202);
         //$this->SetDrawColor(0, 0, 0, 0);
         
         /* LINEAS VERTICALES*/
         //$this->SetDrawColor(255, 0, 0, 255);
         $this->Line($x, 4,$x,58);
         //$this->SetDrawColor(0, 0, 255, 255);
         $this->Line( 12+ $x2, 14, 12+ $x2,30);
         //$this->SetDrawColor(255, 0, 255, 0);
         $this->Line(28 + $x2, 14,28 + $x2,30);
         //$this->SetDrawColor(255, 0, 0, 0);
         $this->Line( 20+ $x2, 30, 20+ $x2,50);
         //$this->SetDrawColor(0, 255, 0, 0);
         //$this->SetDrawColor(0, 0, 0, 0);
         $this->Line(91 + $x2, 4,91 + $x2,58);
         //$this->SetDrawColor(0, 0, 0, 255);
         $this->Line(91 + $x2, 59,91 + $x2,77);
         //$this->SetDrawColor(0, 0, 255, 0);
         $this->Line($x, 59,$x,77);

         /* ORIGINAL */
         //$this->getY(). ' ' . $this->getX(). ' ' .
         $this->Cell(4+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(0, -2, utf8_decode(($emision == "DUPLICADO") ? "DUPLICADO" : "ORIGINAL"));
         $this->Ln(5);

         /* Filas */
         
         /* TIPO COMPROBANTE*/
         $this->Cell(6+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 20);
         $this->Cell(0, 15, utf8_decode($data['tipo_comprobante']));
         $this->Ln(5);
         /* Tipo de Factura */
         $this->SetFont('Arial', 'B', 12); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
         $this->Cell( 20+ $x2); // Movernos a la derecha
         //creamos una celda o fila
         $this->Cell(110, 0, utf8_decode('FACTURA')); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
         $this->Ln(8); // Salto de línea
         /* NOMBRE */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 12);
         $this->Cell(0, -15, utf8_decode("CINTIA ROMINA GIRBINO"), 0, 0, '', 0);
         $this->Ln(1);
         
         /* Domicilio */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, 4, utf8_decode("Riobamba 2751 - 1653 - VILLA BALLESTER - BS.AS"), 0, 0, '', 0);
         $this->Ln(1);

         /* Correo */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(0, 10, utf8_decode("Email: "), 0, 0, '', 0);
         $this->Ln(1);
         $this->Cell(-56+ $x2);  // mover a la derecha
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, 8, utf8_decode("miroperitooficial@gmail.com"), 0, 0, '', 0);
         $this->Ln(1);

         /* TEL */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(0, 13, utf8_decode("Tel: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-58+ $x2);  // mover a la derecha
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, 13, utf8_decode("1140467012"), 0, 0, '', 0);
         $this->Ln(1);
         

         /* Condición IVA */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(0, 18, utf8_decode("Condición frente al IVA: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-38+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, 18, utf8_decode("Monotributista"), 0, 0, '', 0);
         $this->Ln(6);

         /* Comp Nº */
         $this->Cell(57+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(59, -24, utf8_decode("Comp Nº: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(68+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -24, utf8_decode(str_pad($data['numero_comprobante'],8,"0",STR_PAD_LEFT)), 0, 0, '', 0);
         $this->Ln(2);
         

         /* Punto de Venta */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(59, -28, utf8_decode("Punto de Venta:"), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(37+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -28, utf8_decode(str_pad($punto_venta,4,"0",STR_PAD_LEFT)), 0, 0, '', 0);
         $this->Ln(0);

         /* Fecha de emisión */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -15, utf8_decode("Fecha de Emisión: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(39+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -15, utf8_decode(date('d/m/Y')), 0, 0, '', 0);
         $this->Ln(5);

         /* CUIT */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -17, utf8_decode("CUIT: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(26+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -17, utf8_decode($cuit), 0, 0, '', 0);
         $this->Ln(5);


         /* Ingresos Brutos */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -19, utf8_decode("Ingresos Brutos: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(38+ $x2);  // mover a la derecha
         $this->SetFont('Arial', '', 6);
         $this->Cell(85, -19, utf8_decode($ingresos_brutos), 0, 0, '', 0);
         $this->Ln(5);

         /* Fecha de Inicio de Actividades */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -20, utf8_decode("Fecha de Inicio de Actividades: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(52+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -20, utf8_decode($fecha_inicio_actividad), 0, 0, '', 0);
         $this->Ln(1);


         /* Fecha de Vencimiento */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -6, utf8_decode("Fecha de Vto. para el pago: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(48+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -6, utf8_decode($fecha_vto_pago), 0, 0, '', 0);
         $this->Ln(10);

         /* Apellido y Nombre / Razon Social */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -5, utf8_decode("Apellido y Nombre/Razon Social: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-29+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -5, utf8_decode($data['razon_social']), 0, 0, '', 0);
         $this->Ln(5);

         /* Condición IVA */
         $this->Cell(-63 + $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, 0, utf8_decode("Condición IVA: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-47+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, 0, utf8_decode("Consumidor Final"), 0, 0, '', 0);
         $this->Ln(5);

         /* CUIT */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -25, utf8_decode("CUIT: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(27+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -25, utf8_decode("-"), 0, 0, '', 0);
         $this->Ln(5);

         /* Domicilio Comercial */
         $this->Cell( 20+ $x2);  // mover a la derecha
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(85, -20, utf8_decode("Domicilio Comercial: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(42+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(0, -20, utf8_decode($data['direccion']), 0, 0, '', 0);
         $this->Ln(-3);

         /* CAMPOS DE LA TABLA */
         $this->Cell(-63 + $x2);
         $this->SetFillColor(160, 160, 160); //colorFondo
         $this->SetTextColor(0, 0, 0); //colorTexto
         $this->SetDrawColor(0, 0, 0); //colorBorde
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(10, 7, utf8_decode('Código'), 1, 0, 'C', 1);
         $this->Cell(82, 7, utf8_decode('Descripción'), 1, 0, 'C', 1);
         $this->Cell(10, 7, utf8_decode('Cant.'), 1, 0, 'C', 1);
         $this->Cell(16, 7, utf8_decode('Precio Unit.'), 1, 0, 'C', 1);
         $this->Cell(13, 7, utf8_decode('Bonif'), 1, 0, 'C', 1);
         $this->Cell(13, 7, utf8_decode('Subtotal'), 1, 1, 'C', 1);

         $this->SetFillColor(255, 255, 255); //colorFondo
         $this->SetTextColor(0, 0, 0); //colorTexto
         $this->SetDrawColor(0, 0, 0); //colorBorde
         $this->SetFont('Arial', '', 6);
         $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam dui mi, semper ut dignissim ut, tincidunt mollis magna. Suspendisse felis arcu, molestie sed hendrerit quis, ultricies in lectus. Etiam ac rhoncus odio. Quisque et vehicula arcu. Sed non sollicitudin neque, et pharetra tortor.";
         $sql2 = " SELECT p.codigo, p.descripcion, p.precio, vd.cantidad,vd.precio as precio_vd, vd.subtotal FROM ventas_detalle vd LEFT JOIN ventas v ON v.id = vd.id_venta INNER JOIN productos p ON p.id = vd.id_producto WHERE vd.id_venta = $id ";
         $subtotal = 0;
         $ln = 0;
         $cant = 0;
         $array = [];

         for ($i = 0; $i < 10; $i++) {
            $array[] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam dui mi, semper ut dignissim ut, tincidunt mollis magna. Suspendisse felis arcu, molestie sed hendrerit quis, ultricies in lectus. Etiam ac rhoncus odio. Quisque et vehicula arcu. Sed non sollicitudin neque, et pharetra tortor.";
         }
         
         foreach ($array/*$pdo->query($sql2)*/ as $row){
            if($i <= 10){
               $this->Cell(-63 + $x2);
               $this->Cell(10, 7, utf8_decode(/*$row[0]*/ 'CDL'.$cant), 1, 0, 'C', 0);
                  $descripcion=/*$row[1]*/$row;
                  if(strlen($descripcion)>77){
                     $descripcion=substr(/*$row[1]*/$row,0,77)."[...]";
                  }
                  $this->Cell(82, 7, utf8_decode($descripcion), 1, 0, 'L', 0);
                  $this->Cell(10, 7, utf8_decode(/*$row[3]*/$x2), 1, 0, 'C', 0);
                  $this->Cell(16, 7, utf8_decode("$".number_format(/*$row[2]*/'2000', 2,',', '.')), 1, 0, 'C', 0);
                  $this->Cell(13, 7, utf8_decode(/*"$450,00"*/ $ln), 1, 0, 'C', 0);
                  $this->Cell(13, 7, utf8_decode("$".number_format(/*$row[5]*/'2000', 2,',', '.')), 1, 1, 'C', 0);
                  $subtotal= /*$row[5]*/'2000' + $subtotal;
                  $ln = $ln + 7;//Salto de linea que resta del total
                  $cant = $cant + 1;
            }
         }

         $ln = 79 - $ln;
         $this->Ln($ln);//Con 1 solo dato en la tabla el valor seria 144
         $this->Cell(0+ $x2);
         $this->SetFillColor(160, 160, 160); //colorFondo
         $this->SetTextColor(0, 0, 0); //colorTexto
         $this->SetDrawColor(0, 0, 0); //colorBorde
         $this->SetFont('Arial', '', 6); 
         $this->Cell(26+ $x2, 8, utf8_decode("  Subtotal"), 0, 5, '', 1);
         $this->Ln(-4);
         $this->Cell(63+ $x2);
         $this->Cell(0, 0, utf8_decode("$".number_format($subtotal,2, ',', '.')));
         $this->Ln(1);
         $this->Cell(0+ $x2);
         $this->SetFont('Arial', 'B', 8);
         $this->Cell(26+ $x2, 8, utf8_decode("  Total Venta"), 0, 0, '', 1);
         $this->Ln(0);
         $this->Cell(60+ $x2);
         $this->Cell(0, 8, utf8_decode("$".number_format($subtotal,2, ',', '.')));
         /**************************************************************************************/
         /* Lineas Horizontales */
         //$this->SetDrawColor(255, 0, 0, 0);
         $this->Line(10 + $x2, 165,91 + $x2,165);
         //$this->SetDrawColor(0, 0, 255, 0);
         $this->Line(10 + $x2, 178,91 + $x2,178);
         /* Lineas Verticales */
         //$this->SetDrawColor(0, 255, 0, 0);
         $this->Line(10 + $x2, 165,10 + $x2,178);
         //$this->SetDrawColor(0, 0, 0, 255);
         $this->Line(91 + $x2, 165,91 + $x2,178);
         /**********************************************************/
         $this->Ln(10);
         $this->Cell(-63 + $x2);
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(89+ $x2, 8, utf8_decode("Observaciones: "), 1, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-46+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(190, 8, utf8_decode(/*$obs*/ $ln), 0, 0, '', 0);
         $this->Ln(10);
         $this->Cell(-63 + $x2);
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(190, 8, utf8_decode("CAE Nº: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-54+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(190, 8, utf8_decode($data['cae']), 0, 0, '', 0);
         $this->Ln(5);
         $this->Cell(-63 + $x2);
         $this->SetFont('Arial', 'B', 6);
         $this->Cell(190, 8, utf8_decode("Fecha de Vto CAE: "), 0, 0, '', 0);
         $this->Ln(0);
         $this->Cell(-43+ $x2);
         $this->SetFont('Arial', '', 6);
         $this->Cell(190, 8, utf8_decode($data['fecha_vencimiento_cae']), 0, 0, '', 0);
      //}

      // Pie de página
      //function Footer(){
         $this->Ln(6);
         $this->Cell(-63 + $x2);
         //$this->SetY(-125+ $x2); // Posición: a 1,5 cm del final
         $this->SetFont('Arial', 'I', 6); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
         $this->Cell(274, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)
         $this->Ln(0);
         $this->Cell(-63 + $x2);
         //$this->SetY(-125+ $x2); // Posición: a 1,5 cm del final
         $this->SetFont('Arial', 'I', 6); //tipo fuente, cursiva, tamañoTexto
         $this->Cell(120, 10,utf8_decode('2023 @ Desarrollado por Misiones Software'), 0, 0, 'C'); // pie de pagina(fecha de pagina)
      //}
   }
}


$pdf = new PDF('L', 'mm','A4');
//$pdf->AddPage(); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */

//$pdf->AliasNbPages(); //muestra la pagina / y total de paginas
//$pdf->SetFont('Arial', 'B', 8);
//$pdf->SetDrawColor(0, 0, 0); //colorBorde
$pdf->addPage();
$pdf->recibo("ORIGINAL");
$pdf->setY(10);
$pdf->setX(10);
$pdf->recibo("DUPLICADO");
$i = 0;

$i = $i + 1;
/* TABLA */



$pdf->Output('recibo.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)