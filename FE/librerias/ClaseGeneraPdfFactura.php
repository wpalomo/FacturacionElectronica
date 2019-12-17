<?php

include_once 'ClaseGeneraPdf.php';

/**
 * Description of ClaseGeneraPdfFactura
 *
 * @author jpsanchez
 */
class ClaseGeneraPdfFactura extends ClaseGeneraPdf {

    public function __construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos) {
        //echo $tipoReporte;
        //print_r($datosCabecera);
        //print_r($datosDetalle);
        //print_r($datosPagos);
        //print_r($dataVencimientos);
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

        $x = 5;
        $y = 108;
        $this->generarInformacionDetalle($x, $y);

        $x = 5;
        $y = $this->pdf->GetY() + 2;

        if ($y >= 225 || $y >= 275) {
            $this->pdf->AddPage();
            $x = 5;
            $y = 20;
        }

        $auxY = $y;
        $auxH = 0;
        $auxH = $this->generarInformacionAdicional($x, $y);

        $x = 134;
        $y = $auxY;
        $this->generarInformacionTotales($x, $y);

        $y = $this->pdf->GetY() + 2;

        $x = 5;
        $y = $auxY + $auxH + 2;

        if ($y >= 270) {
            $this->pdf->AddPage();
            $x = 5;
            $y = 20;
        }

        $this->generarInformacionFormasPago($x, $y);

        $this->grabarPdf();
    }

    protected function generarInformacionAdicional($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $hRectanguloPieInformacionAdicional = 0;

        $direccionCliente = $this->datosCabecera['CTX_DIRECCION_CLIENTE'];
        $telefonoCliente = $this->datosCabecera['CTX_TELEFONO_CLIENTE'];
        $emailCliente = $this->datosCabecera['CTX_MAIL_CLIENTE'];
        $descripcionPago = $this->datosCabecera['DESCRIPCION_PAGO'];

        if (strlen($direccionCliente) > 0 || strlen($telefonoCliente) > 0 || strlen($emailCliente) > 0 || strlen($descripcionPago) > 0) {
            $yRectanguloPieInformacionAdicional = $y;
            $hRectanguloPieInformacionAdicional = 6;

            if (strlen($direccionCliente) == 0 && strlen($telefonoCliente) == 0 && strlen($emailCliente) == 0) {
                return;
            }

            $tituloAdicional = utf8_decode('Información Adicional');
            $tituloDireccion = utf8_decode('Dirección');
            $tituloTelefono = utf8_decode('Teléfono');
            $tituloEmail = utf8_decode('Email');
            $tituloPago = utf8_decode('Forma de Pago');

            $this->pdf->SetXY($x, $y);
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->Cell(125, 10, $tituloAdicional, 0, 0, 'L');
            $this->pdf->SetFont('');

            $altoCelda = 5;

            if ($direccionCliente != '') {
                $y = $y + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloDireccion, 0, 0, 'L');
                $this->pdf->SetFontSize(7);
                $this->pdf->Cell(95, $altoCelda, $direccionCliente, 0, 0, 'L');
                $this->pdf->SetFontSize(8);

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 9;
            }

            if ($telefonoCliente != '') {
                $x = $auxX;
                $y = $y + 4;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloTelefono, 0, 0, 'L');
                $this->pdf->Cell(95, $altoCelda, $telefonoCliente, 0, 0, 'L');

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 7;
            }

            if ($emailCliente != '') {
                $x = $auxX;
                $y = $y + 4;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloEmail, 0, 0, 'L');
                $this->pdf->Cell(95, $altoCelda, $emailCliente, 0, 0, 'L');

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 3;
            }

            if ($descripcionPago != '') {
                $x = $auxX;
                $y = $y + 4;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloPago, 0, 0, 'L');
                $this->pdf->Cell(95, $altoCelda, $descripcionPago, 0, 0, 'L');

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 4;
            }

            if (is_array($this->dataVencimientos) && count($this->dataVencimientos) > 0) {
                foreach ($this->dataVencimientos as $key => $value) {
                    $x = $auxX;
                    $y = $y + 4;
                    $this->pdf->SetXY($x, $y);

                    $this->pdf->Cell(30, $altoCelda, str_pad($value['VENCIMIENTOS'], 25), 0, 0, 'L');
                    $this->pdf->Cell(30, $altoCelda, str_pad($value['DESCRIPCION'], 30), 0, 0, 'L');
                    $this->pdf->Cell(30, $altoCelda, '$ ' . $value['VALOR'], 0, 0, 'L');

                    $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 3;
                }
            }

            $xRectanguloPieInformacionAdicional = $auxX;
            $wRectanguloPieInformacionAdicional = 125;

            $this->pdf->Rect($xRectanguloPieInformacionAdicional, $yRectanguloPieInformacionAdicional, $wRectanguloPieInformacionAdicional, $hRectanguloPieInformacionAdicional);
        }

        return $hRectanguloPieInformacionAdicional;
    }

}
