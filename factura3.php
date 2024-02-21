<?php
require_once('vendor/fpdf/fpdf.php');

class Factura extends FPDF {
    /*function Header() {
        // Encabezado con bordes
        $this->SetLineWidth(1);
        $this->SetFillColor(255,255,255);
        $this->Rect(10,10,190,30,'DF');
        // Copia
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 10);
        $this->Cell(50,10,'Copia: ',0,0,'L');
        // Tipo de factura y código
        $this->SetFont('Arial','B',12);
        $this->SetXY(100, 15);
        $this->Cell(50,10,'Tipo de factura: ',0,0,'L');
        $this->SetXY(160, 15);
        $this->Cell(30,10,'0000001',0,0,'L');
    }*/

    function FacturaHeader2() {
        // Logotipo
        //$this->Image('logo.png',10,10,30);
        $this->Image('../images/logo/logo.png',10,10,30);
        // Encabezado
        $this->SetFont('Arial','B',15);
        $this->SetXY(40, 10);
        $this->Cell(100,10,'Factura',0,0,'C');
        // Datos de la empresa
        $this->SetFont('Arial','',12);
        $this->SetXY(10, 20);
        $this->Cell(50,10,'Empresa S.A.',0,0,'L');
        $this->SetXY(10, 25);
        $this->Cell(50,10,'Domicilio Comercial',0,0,'L');
        $this->SetXY(10, 30);
        $this->Cell(50,10,'Condicion frente al IVA',0,0,'L');
        // Datos de la factura
        $this->SetFont('Arial','',12);
        $this->SetXY(60, 20);
        $this->Cell(50,10,'Tipo de comprobante: A',0,0,'L');
        $this->SetXY(60, 25);
        $this->Cell(50,10,'Punto de venta: 0001',0,0,'L');
        $this->SetXY(60, 30);
        $this->Cell(50,10,'Numero de comprobante: 123456',0,0,'L');
    }

    function FacturaHeader() {
        // Logotipo
        $this->Image('../images/logo/logo.png',10,10,30);
        // Encabezado
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Factura',0,0,'C',0);
        $this->Ln();
        //tabla de datos de la empresa
        $this->SetFont('Arial','',12);
        $this->Cell(50,10,'Empresa S.A.',1,0,'L',false, '', 0, false, 'T', 'T');
        $this->Cell(50,10,'Tipo de comprobante: A',1,0,'L',false, '', 0, false, 'T', 'T');
        $this->Ln();
        $this->Cell(50,10,'Domicilio Comercial',1,0,'L',false, '', 0, false, 'T', 'T');
        $this->Cell(50,10,'Punto de venta: 0001',1,0,'L',false, '', 0, false, 'T', 'T');
        $this->Ln();
        $this->Cell(50,10,'Condicion frente al IVA',1,0,'L',false, '', 0, false, 'T', 'T');
        $this->Cell(50,10,'Numero de comprobante: 123456',1,0,'L',false, '', 0, false, 'T', 'T');
    }



    function InformacionEmpresa() {
        // Sector con la información de la empresa
        $this->SetLineWidth(1);
        $this->SetFillColor(255,255,255);
        $this->Rect(10,40,190,40,'DF');
        // Logo
        $this->Image('../images/logo/logo.png',15,45,40);
        // Razón social
        $this->SetFont('Arial','B',12);
        $this->SetXY(60, 45);
        $this->Cell(50,10,'Razón Social: Mi Empresa S.A.',0,0,'L');
        // Domicilio comercial
        $this->SetFont('Arial','',12);
        $this->SetXY(60, 50);
        $this->Cell(50,10,'Domicilio Comercial: Calle 123',0,0,'L');
        // Condiciones frente al IVA
        $this->SetFont('Arial','',12);
        $this->SetXY(60, 55);
        $this->Cell(50,10,'Condiciones frente al IVA: Responsable Inscripto',0,0,'L');
    }

    function PeriodoFacturado() {
        // Encabezado con bordes
        $this->SetLineWidth(1);
        $this->SetFillColor(255,255,255);
        $this->Rect(10,80,190,20,'DF');
        // Periodo facturado
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 80);
        $this->Cell(50,10,'Periodo facturado:',0,0,'L');
        // Desde
        $this->SetFont('Arial','',12);
        $this->SetXY(80, 80);
        $this->Cell(50,10,'Desde: 01/01/2022',0,0,'L');
        // Hasta
        $this->SetFont('Arial','',12);
        $this->SetXY(130, 80);
        $this->Cell(50,10,'Hasta: 31/01/2022',0,0,'L');
        // Fecha de vencimiento
        $this->SetFont('Arial','',12);
        $this->SetXY(160, 80);
        $this->Cell(30,10,'Fecha de vencimiento: 20/02/2022',0,0,'L');
    }

    function InformacionCliente() {
        // Encabezado con bordes
        $this->SetLineWidth(1);
        $this->SetFillColor(255,255,255);
        $this->Rect(10,100,190,40,'DF');
        // CUIT
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 100);
        $this->Cell(50,10,'CUIT:',0,0,'L');
        $this->SetFont('Arial','',12);
        $this->SetXY(30, 100);
        $this->Cell(50,10,'30-11111111-3',0,0,'L');
        // Razón Social
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 110);
        $this->Cell(50,10,'Razón Social:',0,0,'L');
        $this->SetFont('Arial','',12);
        $this->SetXY(50, 110);
        $this->Cell(50,10,'Empresa S.A.',0,0,'L');
        // Domicilio
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 120);
        $this->Cell(50,10,'Domicilio:',0,0,'L');
        $this->SetFont('Arial','',12);
        $this->SetXY(40, 120);
        $this->Cell(50,10,'Calle Falsa 123',0,0,'L');
        // Condición frente al IVA
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 130);
        $this->Cell(50,10,'Condición frente al IVA:',0,0,'L');
        $this->SetFont('Arial','',12);
        $this->SetXY(70, 130);
        $this->Cell(50,10,'Responsable Inscripto',0,0,'L');
    }

    function Footer() {
        // Pie de página
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
    function FacturaBody() {
        // Datos de la factura
        $this->SetFont('Arial','',12);
        $this->Cell(40,10,'Número de factura: 1001');
        $this->Ln();
        $this->Cell(40,10,'Fecha: 01/01/2021');
        $this->Ln();
        $this->Cell(40,10,'Cliente: Juan Pérez');
        $this->Ln();
        $this->Cell(40,10,'Domicilio: Calle Falsa 123');
        $this->Ln();
        $this->Cell(40,10,'Ciudad: Ciudad Falsa');
        $this->Ln();
        $this->Cell(40,10,'País: País FALSO');
        $this->Ln(20);
        // Tabla de detalles de la factura
        $this->SetFont('Arial','B',12);
        $this->Cell(40,10,'Cantidad',1,0,'C');
        $this->Cell(60,10,'Descripción',1,0,'C');
        $this->Cell(40,10,'Precio Unitario',1,0,'C');
        $this->Cell(40,10,'Total',1,1,'C');
        $this->SetFont('Arial','',12);
        $this->Cell(40,10,'1',1,0,'C');
        $this->Cell(60,10,'Artículo 1',1,0,'L');
        $this->Cell(40,10,'$10.00',1,0,'C');
        $this->Cell(40,10,'$10.00',1,1,'C');
        $this->Cell(40,10,'2',1,0,'C');
        $this->Cell(60,10,'Artículo 2',1,0,'L');
        $this->Cell(40,10,'$5.00',1,0,'C');
        $this->Cell(40,10,'$10.00',1,1,'C');
        $this->Ln();
        $this->Cell(140,10,'',0,0,'C');
        $this->Cell(40,10,'Total: $20.00',1,1,'C');
    }
}

// Crea una nueva factura
$pdf = new Factura();
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->FacturaBody();
$pdf->FacturaHeader();
$pdf->InformacionEmpresa();
$pdf->PeriodoFacturado();
$pdf->InformacionCliente();
$pdf->Output();
