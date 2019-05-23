<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'ClaseUtilidades.php';
include_once 'ClaseSesion.php';
include_once 'funciones.php';

/**
 * Description of ClaseLogin
 *
 * @author jpsanchez
 */
class ClaseLogin {

    private $login;
    private $clave;

    public function __construct($login, $clave) {
        $this->login = mssql_real_escape_string($login);
        $this->clave = mssql_real_escape_string($clave);
    }

    public function login() {
        session_start();
        $_SESSION['S_loginOk'] = 'NO';
        $_SESSION['S_id_usuario'] = '';
        $_SESSION['S_id_sesion'] = '';
        //$_SESSION['S_mn_codigo'] = '';
        $_SESSION['S_login'] = '';
        $_SESSION['S_id_perfil'] = '';
        $_SESSION['S_descripcion_perfil'] = '';
        $_SESSION['S_nombre_apellido'] = '';

        $query = "
            EXEC SP_GEN_LOGIN
            @in_login = '$this->login',
            @in_clave = '$this->clave',
            @in_operacion = 'LOG'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] == 'N') {
            if ($result['ok'] == 'S') {
                $data = $result['data'];

                $id_usuario = $data[0]['id_usuario'];
                $sesionIniciada = 'N';

                $objetoSesion = new ClaseSesion();
                $resultSesion = $objetoSesion->ingresarSesion($id_usuario);

                if ($resultSesion['error'] == 'S') {
                    return $resultSesion;
                } else {
                    $resultSesion['data'][0]['loginOk'] = "SI";
                    $resultSesion['data'][0]['sesionIniciada'] = "S";

                    $dataSesion = $resultSesion['data'];

                    $_SESSION['S_loginOk'] = 'SI';
                    $_SESSION['S_id_usuario'] = $id_usuario;
                    $_SESSION['S_id_sesion'] = $dataSesion['id_sesion'];
                    $_SESSION['S_login'] = $dataSesion['login'];
                    $_SESSION['S_id_perfil'] = $dataSesion['id_perfil'];
                    $_SESSION['S_descripcion_perfil'] = $dataSesion['descripcion_perfil'];
                    $_SESSION['S_nombre_apellido'] = $dataSesion['nombre_apellido'];
                    /*
                      $array = array(
                      "success" => true,
                      //"loginOk" => $_SESSION['S_loginOk'],
                      "sesionIniciada" => 'S',
                      "id_usuario" => $us_codigo,
                      "se_codigo" => $_SESSION['S_se_codigo'],
                      "us_login" => $_SESSION['S_us_login'],
                      "pe_codigo" => $_SESSION['S_pe_codigo'],
                      "pe_desc" => $_SESSION['S_pe_desc'],
                      "us_nombres_apellidos" => $_SESSION['S_us_nombres_apellidos'],
                      "ok" => $data1['ok'],
                      "message" => $resp['message']
                      );
                     */

                    return $resultSesion;
                }

                //print_r($resultSesion);
            }
        }

        return $result;

        /* if ($result['error'] != 'N') {
          return $result;
          } else {
          if ($result['ok'] == 'S') {
          print_r($result);
          }

          } */
    }

}
