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
                $id_usuario = $data[0]['id_usuario'];
                $sesionIniciada = 'N';

                $objetoSesion = new ClaseSesion();
                $resp = $objetoSesion->ingresarSesion($id_usuario);
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
