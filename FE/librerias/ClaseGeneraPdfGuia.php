<?php

include_once 'ClaseGeneraPdf.php';

/**
 * Description of ClaseGeneraPdfGuia
 *
 * @author jpsanchez
 */
class ClaseGeneraPdfGuia extends ClaseGeneraPdf {

    public function __construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos) {
        parent::__construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos);
    }

    public function generaPdf() {
        $x = 10;
        $y = 10;
        $w = 80;
        $h = 20;
        $this->generarLogo($x, $y, $w, $h);

        $x = 10;
        $y = 48;
        $this->generarInformacionEmpresa($x, $y);

        $x = 110;
        $y = 10;
        $this->generarInformacionFactura($x, $y);

        $x = 10;
        $y = 90;
        $this->generarInformacionCliente($x, $y);

        $x = 10;
        $y = 117;
        $this->generarInformacionDetalle($x, $y);

        $x = 5;
        $y = $this->pdf->GetY() + 4;

        if ($this->pdf->GetY() + 5 > $this->getPageBreakTrigger()) {
            $this->pdf->AddPage();
            $x = 5;
            $y = 20;
        }

        $auxY = $y;
        $auxH = 0;
        $auxH = $this->generarInformacionAdicional($x, $y);

        $this->grabarPdf();
    }

    //metodo sobreescrito
    protected function generarInformacionCliente($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosCliente = $x - 5;
        $yRectanguloDatosCliente = $y - 4;
        $wRectanguloDatosCliente = 200;
        $hRectanguloDatosCliente = 25;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación (Transportista)"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_RUC_TRANSP']);

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50; //60;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CNO_PERSONA_TRANSP']));

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Placa:"));

        $x = $auxX + 50; //60;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CTX_PLACA_TRANSP']));

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Punto de Partida:"));

        $x = $auxX + 50; //60;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CTX_PTO_PARTIDA']));

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha inicio Transporte"));

        $x = $auxX + 50; //60;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['DFM_INI_TRASLADO']);

        $x = $auxX + 140; //150;        
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha fin Transporte:"));

        $x = $auxX + 175; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['DFM_TER_TRASLADO']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

    protected function generarInformacionDetalle($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectangulo = $x - 5;
        $yRectangulo = $y - 4;
        $wRectangulo = 200;
        $hRectangulo = 2;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Comprobante de Venta:"));

        $x = $auxX + 140; //150;        
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha de Emisión:"));

        $hRectangulo = $hRectangulo + 4;

        $x = $auxX;
        $y = $auxY + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Número de Autorización:"));

        $hRectangulo = $hRectangulo + 4;

        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Motivo Traslado:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['MOTIVO_TRASLADO']));

        $hRectangulo = $hRectangulo + 4;

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Destino(Punto de llegada)"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CTX_PTO_LLEGADA']));

        $hRectangulo = $hRectangulo + 4;

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación(Destinatario)"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_RUC_CLIENTE']);

        $hRectangulo = $hRectangulo + 4;

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CNO_CLIENTE']));

        $hRectangulo = $hRectangulo + 4;

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Documento Aduanero"));

        $hRectangulo = $hRectangulo + 4;

        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Código Establecimiento Destino"));

        $hRectangulo = $hRectangulo + 4;

        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Ruta:"));

        $hRectangulo = $hRectangulo + 4;
        $y = $y + 2;

        $x = $x + 10;
        $altoCelda = 5;

        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(20, $altoCelda, 'Cantidad', 1, 0, 'C');
        $this->pdf->Cell(105, $altoCelda, utf8_decode('Déscripcion'), 1, 0, 'C');
        $this->pdf->Cell(25, $altoCelda, utf8_decode('Código Principal'), 1, 0, 'C');
        $this->pdf->Cell(25, $altoCelda, utf8_decode('Código Auxiliar'), 1, 0, 'C');

        $hRectangulo = $hRectangulo + $altoCelda;
        $y = $this->pdf->GetY() + $altoCelda;
        $this->pdf->SetXY($x, $y);

        $y2 = $y;

        foreach ($this->datosDetalle as $keyDetalle => $valueDetalle) {
            $x = $auxX + 10;
            $this->pdf->SetX($x);
            $solicitada = number_format($valueDetalle['NQN_SOLICITADA'], 2, '.', ',');
            $cno_item = utf8_decode($valueDetalle['CNO_ITEM']);

            $this->pdf->Cell(20, $altoCelda, $solicitada, 1, 0, 'C');

            if (strlen($cno_item) <= 58) {
                $this->pdf->Cell(105, $altoCelda, $cno_item, 1, 0, 'L');
            } else {
                $this->myCell2(105, $altoCelda, $cno_item);
            }

            $this->pdf->Cell(25, $altoCelda, $valueDetalle['CCI_ITEM'], 1, 0, 'C');
            $this->pdf->Cell(25, $altoCelda, $valueDetalle['CODIGO_AUXILIAR'], 1, 0, 'C');

            $y2 = $this->pdf->GetY();
            $hRectangulo = $hRectangulo + $altoCelda;

            if ($y2 >= $this->getPageBreakTrigger() - 10) {
                $hRectangulo = $hRectangulo + 3;
                $this->pdf->Rect($xRectangulo, $yRectangulo, $wRectangulo, $hRectangulo);

                //$this->pdf->AddPage();
                $xRectangulo = 5;
                $yRectangulo = 5;
                $hRectangulo = 0;
            }

            $this->pdf->Ln();

            $y2 = $this->pdf->GetY();
        }

        $hRectangulo = $hRectangulo + 2;

        $this->pdf->Rect($xRectangulo, $yRectangulo, $wRectangulo, $hRectangulo);
    }

    protected function generarInformacionAdicional($x, $y) {
        $y = $this->pdf->GetY();

        $hRectanguloPieInformacionAdicional = 0;

        $observacion = utf8_decode($this->datosCabecera['CNO_OBSERVACION']);
        $observacion2 = utf8_decode($this->datosCabecera['CNO_OBSERVACION_2']);

        //calcular informacion adicional entra en la pagina o se la imprime
        //en una nueva pagina
        if (strlen($observacion) > 0 || strlen($observacion2) > 0) {
            $auxAlto = 6; //Label Informacion Adicional

            if ($observacion != '') {
                $auxAlto = $auxAlto + 14;
            }

            if ($observacion2 != '') {
                $auxAlto = $auxAlto + 14;
            }

            if ($y + $auxAlto > $this->getPageBreakTrigger() - 10) {
                $this->pdf->AddPage();
                $y = $this->pdf->GetY();
            }
        }

        $y = $y + 4;

        $auxX = $x;
        $auxY = $y;

        if (strlen($observacion) > 0 || strlen($observacion2) > 0) {
            $yRectanguloPieInformacionAdicional = $y;
            $hRectanguloPieInformacionAdicional = 6;

            $tituloAdicional = utf8_decode('Información Adicional');
            $tituloObservacion = utf8_decode('Observación:');

            $this->pdf->SetXY($x, $y);
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->Cell(125, 10, $tituloAdicional, 0, 0, 'L');
            $this->pdf->SetFont('');

            if ($observacion != '') {
                $y = $y + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->SetFont('Arial', 'B', 8);
                $this->pdf->Cell(125, 10, $tituloObservacion, 0, 0, 'L');

                $y = $y + 7;
                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->SetFont('');

                $this->myCell3(105, 10, $observacion, 150);

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 10;
            }

            $tituloObservacion2 = utf8_decode('Adicional:');

            if ($observacion2 != '') {
                $y = $y + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->SetFont('Arial', 'B', 8);
                $this->pdf->Cell(125, 10, $tituloObservacion2, 0, 0, 'L');

                $y = $y + 7;
                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->SetFont('');
                $this->myCell3(105, 10, $observacion2, 150);

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 10;
            }

            $xRectanguloPieInformacionAdicional = $auxX;
            $wRectanguloPieInformacionAdicional = 200;

            $this->pdf->Rect($xRectanguloPieInformacionAdicional, $yRectanguloPieInformacionAdicional, $wRectanguloPieInformacionAdicional, $hRectanguloPieInformacionAdicional);
        }

        return $hRectanguloPieInformacionAdicional;
    }

}
