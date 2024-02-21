<?php
require_once('vendor/fpdf/fpdf.php');

class Factura extends FPDF {
    function Header() {
        // Encabezado de la factura
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10,'Factura',0,0,'C');
        $this->Ln(20);
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
$pdf->FacturaBody();
$pdf->Output();
