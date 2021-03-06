<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClaseJson
 *
 * @author jpsanchez
 */
class ClaseJson {

    public static function getJson($datos) {
        $success = false;
        if (array_key_exists('error', $datos)) {
            if ($datos['error'] == 'N') {
                $success = true;
            }
        }

        $total = count($datos['data']);
        if (array_key_exists('total', $datos)) {
            $total = $datos['total'];
        }

        $result = array(
            "success" => $success,
            "total" => $total,
            "totalFiltro" => count($datos['data']),
            "data" => $datos['data'],
            "mensaje" => $datos['mensaje']
        );

        if (array_key_exists('ok', $datos)) {
            $result['ok'] = $datos['ok']; //$new_input['ok'];
        }

        return json_encode($result);
    }

    public static function getMessageJson($success = false, $cadena = '', $cadena2 = '') {
        $result = array(
            "success" => $success,
            "mensaje" => $cadena,
            "mensaje2" => $cadena2
        );

        return json_encode($result);
    }

    public static function getArrayJson($array) {
        return json_encode($array);
    }

}
