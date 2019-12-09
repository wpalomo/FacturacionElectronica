<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';

/**
 * Description of ClaseValidaciones
 *
 * @author jpsanchez
 */
class ClaseValidaciones {

    private static $dataDirectorios;
    private static $arrayTipoCmpr = array();
    private $errorDB;

    public static function crearDirectorios() {
        //$this->arrayTipoCmpr = array('FAC', 'NC', 'RET', 'GUI');
        self::$arrayTipoCmpr = array('FAC', 'NC', 'RET', 'GUI');

        if (($error = self::getDirectorios()) == 'S') {
            return $this->errorDB;
        } else {
            foreach (self::$dataDirectorios as $keyDirectorio => $valueDirectorio) {
                foreach (self::$arrayTipoCmpr as $valueTipoCmpr) {
                    self::crear($valueDirectorio['CCI_RUTA_GENERADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_FIRMADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_ENVIADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_ENVIADOS_RECHAZADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_AUTORIZADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_NO_AUTORIZADOS'] . $valueTipoCmpr);
                    self::crear($valueDirectorio['CCI_RUTA_PDF'] . $valueTipoCmpr);
                }
            }
        }
    }

    private function getDirectorios() {
        $query = "
            EXEC BIZ_FAC..SP_FE_PARAMETROS            
            @IN_OPERACION = 'QRT'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            self::$dataDirectorios = $result['data'];
        }
    }

    private function crear($ruta) {        
        echo 'VERIFICANDO RUTA: ' . $ruta . '<br>';
        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0777, true)) {
                echo 'Error al crear el directorio: ' . $ruta;
            }
        }

        echo '<hr>';
    }

}
