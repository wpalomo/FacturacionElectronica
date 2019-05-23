<?php

/**
 * Description of ClaseSesion
 *
 * @author jpsanchez
 */
include_once 'Mobile-Detect-2.8.33/Mobile_Detect.php';

class ClaseSesion {

    /**
     * Registra una sesion en la base de datos para posteriormente con esta
     * sesion validar si el usuario puede realizar transacciones en el sistema
     * @param string $id_usuario codigo del usuario que inicia la sesion
     * @return json
     */
    public function ingresarSesion($id_usuario) {
        $detect = new Mobile_Detect();
        $dispositivo = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

        $ip = $this->getIP();

        //echo $dispositivo;
        //echo $ip;

        $query = "
            EXEC SP_GEN_SESION
            @in_id_usuario = '$id_usuario',                
            @in_ip = '$ip',                 
            @in_usuario_ing_act = '$id_usuario',
            @in_operacion = 'I'
        ";

        //echo $query;

        $parametros = array(
            'query' => $query,
            'autocommit' => true
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    /**
     * Retorna una direccion IP
     * @return string
     */
    public function getIP() {
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

}
