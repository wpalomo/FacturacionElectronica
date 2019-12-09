<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';

/**
 * Description of ClaseProcesoFE
 *
 * @author jpsanchez
 */
class ClaseProcesoFE {

    public function __construct() {
        ClaseValidaciones::crearDirectorios();
    }

}
