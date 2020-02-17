<?php

// TODO: poner validaciones para los datos que llegan, no olvidar codificar la clave.
include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseUsuario
 *
 * @author jpsanchez
 */
class ClaseUsuario {

    public function getUsuarios($parametros) {

//        print_r($parametros);

        $select = "
            select *
            from VW_USUARIOS_PERFILES
        ";

        $where = " WHERE id_usuario > 0 and estado_usuario != 'X' ";

        $selectTotalRegistros = "
            select count(*) as total_registros
            from VW_USUARIOS_PERFILES
        ";

        $records = json_decode(stripslashes($parametros['filters']), true);

        foreach ($records as $k => $value) {
            $valor = '';
            $tmp = '';
            foreach ($value as $key => $_value) {

                if (is_array($_value)) {
                    $tmp = $_value;
                } else {
                    if ($key == 'value') {
                        $valor = $_value;
                    }
                }

                if ($key == 'matchMode') {
                    switch ($_value) {
                        case 'startsWith':
                            $where = $where . " AND " . $k . " like '$valor%' ";
                            break;
                        case 'contains':
                            $where = $where . " AND " . $k . " like '%$valor%' ";
                            break;
                        case 'equals':
                            if (is_array($tmp)) {
                                $condicion = true;
                                foreach ($tmp as $k2 => $v2) {
                                    if ($k2 == 'value') {
                                        $valor = $v2;
                                    }

                                    if ($k == 'estado_usuario' && $valor == 'T') {
                                        $condicion = false;
                                        continue;
                                    }
                                }

                                if ($condicion) {
                                    $where = $where . " AND " . $k . " = '$valor' ";
                                }
                            } else {
                                $where = $where . " AND " . $k . " = '$valor' ";
                            }
                            break;
                        case 'in':
                            if (is_array($tmp)) {
                                $cadenaIn = " IN(";
                                foreach ($tmp as $valueIn) {
                                    $cadenaIn = $cadenaIn . "'$valueIn'";

                                    if (next($tmp) == true) {
                                        $cadenaIn = $cadenaIn . ",";
                                    } else {
                                        $cadenaIn = $cadenaIn . ") ";
                                    }
                                }
                                //echo $cadenaIn;
                                $where = $where . " AND " . $k . $cadenaIn;
                            }
                            break;
                    }
                }
            }
        }

        $start = $parametros['start'];

        $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
        if ($parametros['sortOrder'] == '-1') {
            $order = $order . ' DESC ';
        }

        //$offset = 'OFFSET ' . (($start - 1) * $parametros['limit']) . ' ROWS ';
        $offset = 'OFFSET ' . ($start) . ' ROWS ';
        $fetch = 'FETCH NEXT ' . $parametros['limit'] . ' ROWS ONLY';


        $queryTotalRegistros = $selectTotalRegistros . $where;
        $query = $select . $where . $order . $offset . $fetch;

        $parametros = array(
            'query' => $queryTotalRegistros
        );

        $resultTotal = ClaseBaseDatos::query($parametros);

//        print_r($resultTotal);

        if ($resultTotal['error'] == 'N') {
            $dataTotal = $resultTotal['data'];
            $totalRegistros = $dataTotal[0]['total_registros'];

            $parametros = array(
                'query' => $query,
                'total' => $totalRegistros
            );

            $result = ClaseBaseDatos::query($parametros);

//            print_r($result);
            return $result;
        } else {
            return $resultTotal;
        }
    }

    public function insert($parametros) {
        $usuario = json_decode(stripslashes($parametros['usuario']), true);

        $id_perfil = mssql_real_escape_string($usuario['id_perfil']);
        $login = mssql_real_escape_string($usuario['login']);
        $clave = mssql_real_escape_string($usuario['clave']);
        $nombre = mssql_real_escape_string($usuario['nombre']);
        $apellido = mssql_real_escape_string($usuario['apellido']);
        $email = mssql_real_escape_string($usuario['email']);
        $estado_usuario = mssql_real_escape_string($usuario['estado_usuario']);
        $cambio_clave = mssql_real_escape_string($usuario['cambio_clave']);

        $clave = substr(crypt($clave, strtoupper($login)), 3);

        $query = "
            EXEC SP_GEN_USUARIOS
            @in_id_perfil = '$id_perfil',
            @in_login = '$login',
            @in_clave = '$clave',  
            @in_nombre = '$nombre',
            @in_apellido = '$apellido',
            @in_email = '$email',
            @in_estado_usuario = '$estado_usuario',
            @in_operacion = 'I'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function update($parametros) {
        $usuario = json_decode(stripslashes($parametros['usuario']), true);

        $id_usuario = mssql_real_escape_string($usuario['id_usuario']);
        $id_perfil = mssql_real_escape_string($usuario['id_perfil']);
        $login = mssql_real_escape_string($usuario['login']);
        $clave = mssql_real_escape_string($usuario['clave']);
        $nombre = mssql_real_escape_string($usuario['nombre']);
        $apellido = mssql_real_escape_string($usuario['apellido']);
        $email = mssql_real_escape_string($usuario['email']);
        $estado_usuario = mssql_real_escape_string($usuario['estado_usuario']);
        $cambiar_clave = mssql_real_escape_string($usuario['cambiar_clave']);

        $clave = substr(crypt($clave, strtoupper($login)), 3);

        $query = "
            EXEC SP_GEN_USUARIOS
            @in_id_usuario = '$id_usuario',
            @in_id_perfil = '$id_perfil',
            @in_clave = '$clave',  
            @in_nombre = '$nombre',
            @in_apellido = '$apellido',
            @in_email = '$email',
            @in_estado_usuario = '$estado_usuario',
            @in_cambiar_clave = '$cambiar_clave',             
            @in_operacion = 'U'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function delete($parametros) {
        $usuario = json_decode(stripslashes($parametros['usuario']), true);

        $id_usuario = mssql_real_escape_string($usuario['id_usuario']);

        $query = "
            EXEC SP_GEN_USUARIOS
            @in_id_usuario = '$id_usuario',
            @in_operacion = 'D'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function uploadImagen($files, $login) {
        $usuariosDir = 'imagenes/usuarios/';

        $fileName = $files['picture']['name'];
        $tmpName = $files['picture']['tmp_name'];
        $fileSize = $files['picture']['size'];
        $fileType = $files['picture']['type'];

        $trozos = explode(".", $fileName);
        $extension = end($trozos);

        $imagen = $login . '.' . $extension;

        if (file_exists($usuariosDir . $imagen)) {
            unlink($usuariosDir . $imagen) or die("Problemas al borrar el archivo");
        }

        if (move_uploaded_file($files["picture"]["tmp_name"], $usuariosDir . $imagen)) {

            $login = mssql_real_escape_string($login);

            $query = "
            EXEC SP_GEN_USUARIOS
            @in_login = '$login',
            @in_imagen_aux = '$imagen',      
            @in_operacion = 'UPI'
        ";

            echo $query;
            
            $parametros = array(
                'query' => $query
            );

            $result = ClaseBaseDatos::query($parametros);

            return $result;
        } else {
            //return ClaseBaseDatos:;
        }
    }

}
