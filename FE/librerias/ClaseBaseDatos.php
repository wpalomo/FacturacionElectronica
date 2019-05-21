<?php

error_reporting(E_ERROR | E_PARSE);
//error_reporting(0);
date_default_timezone_set("America/Guayaquil");

/**
 * Description of ClaseBaseDatos
 *
 * @author jpsanchez
 */
class ClaseBaseDatos {

    private static $mssql;
    private static $servidor;
    private static $base;
    private static $usuario;
    private static $clave;
    private static $autocommit;

    private static function conectarse($parametros) {
        $interfaz = '';
        $autocommit = false;

        /*
         * se busca si existe otra interfaz, caso contrario se coge la interfaz default
         */
        if (array_key_exists('interfaz', $parametros)) {
            $interfaz = $parametros['interfaz'];
        }

        /*
         * se busca si existe el parametro autocommit, para control de transacciones
         */
        if (array_key_exists('autocommit', $parametros)) {
            $autocommit = $parametros['autocommit'];
        }

        switch ($interfaz) {
            case '':
                self::$servidor = _SERVIDOR;
                self::$base = _BASE;
                self::$usuario = _USUARIO;
                self::$clave = _CLAVE;
                break;
            case 'I':
                self::$servidor = _SERVIDORI;
                self::$base = _BASEI;
                self::$usuario = _USUARIOI;
                self::$clave = _CLAVEI;
                break;
        }

        if ($autocommit) {
            self::$autocommit = true;
        }

        $conn = odbc_connect("Driver={SQL Server};Server=" . self::$servidor . ";Database=" . self::$base . ";", self::$usuario, self::$clave);

        if ($conn) {
            self::$mssql = $conn;

            $result = array(
                "error" => 'N',
                "mensaje" => "Conexión Exitosa"
                    //"mensaje" => array("reason" => "Conexión Exitosa")
            );

            return $result;
        } else {
            return self::getError();
        }
    }

    private static function desconectarse() {
        odbc_close(self::$mssql);
    }

    public static function query($parametros) {
        $continuar = false;

        if (array_key_exists('verificaPermisos', $parametros)) {
            $permisos = $parametros['verificaPermisos'];

            $resp = self::verificaSesionPermiso($permisos);

            if ($resp['error'] != 'N' || $resp['ok'] == 'N') {
                return $resp;
            }

            $continuar = true;
        } else {
            $continuar = true;
        }

        if ($continuar) {
            $result = self::conectarse($parametros);
            $query = $parametros['query'];

            if ($result['error'] == 'N') {

                if (self::$autocommit) {
                    self::autocommit(true);
                }

                //echo $query;
                $resp = odbc_exec(self::$mssql, $query);

                $mensaje = '';
                $ok = '';

                if ($resp[0]['mensaje']) {
                    $mensaje = $resp[0]['mensaje'];
                }

                if (!odbc_error()) {
                    if (self::$autocommit) {
                        self::commit();
                    }

                    while ($row = odbc_fetch_array($resp)) {
                        $registros[] = array_map('utf8_encode', $row);
                    }

                    if (array_key_exists('mensaje', $registros[0])) {
                        $mensaje = $registros[0]['mensaje'];
                    }

                    if (array_key_exists('ok', $registros[0])) {
                        $ok = $registros[0]['ok'];
                    }

                    $result = array(
                        "error" => 'N',
                        "ok" => $ok,
                        "data" => $registros,
                        "mensaje" => $mensaje
                            //"mensaje" => array("reason" => $mensaje)
                    );

                    self::desconectarse();
                    return $result;
                } else {
                    $result = self::getError();

                    if (self::$autocommit) {
                        self::rollback();
                    }

                    self::desconectarse();
                    return $result;
                }
            }

            self::desconectarse();
            return $result;
        }
    }

    private static function verificaSesionPermiso($permisos) {
        $objetoSesion = new claseSesion();
        $result = $objetoSesion->verificaSesionPermiso($permisos['S_se_codigo'], $permisos['S_us_codigo'], $permisos['mn_codigo']);

        //print_r($permisos);
        //print_r($result);

        return $result;

//        if ($result['error'] == 'N') {
//            echo ClaseJson::getJson($result);
//        } else {
//            echo (ClaseJson::getMessageJson(false, $result['message']));
//        }
    }

    private static function getError() {
        $result = array(
            "error" => 'S',
            //"message" => utf8_encode(odbc_error() . ' - ' . odbc_errormsg())
            "mensaje" => utf8_encode(odbc_error() . ' - ' . odbc_errormsg())
                //"mensaje" => array("reason" => utf8_encode(odbc_error() . ' - ' . odbc_errormsg()))
        );

        return $result;
    }

    private static function autocommit($autocommit = false) {
        odbc_autocommit(self::$mssql, $autocommit);
    }

    private static function commit() {
        odbc_commit(self::$mssql);
    }

    private static function rollback() {
        odbc_rollback(self::$mssql);
    }

}

?>