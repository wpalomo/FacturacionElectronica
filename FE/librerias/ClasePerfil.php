<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClasePerfil
 *
 * @author jpsanchez
 */
class ClasePerfil {

    private $id_perfil;
    private $descripcion_perfil;
    private $estado_perfil;

//    public function __construct($id_perfil, $descripcion_perfil, $estado_perfil) {
//        $this->id_perfil = mssql_real_escape_string($id_perfil);
//        $this->descripcion_perfil = mssql_real_escape_string($descripcion_perfil);
//        $this->estado_perfil = mssql_real_escape_string($estado_perfil);
//    }

    public function getPerfiles($parametros) {

//        print_r($parametros);

        $select = "
            select *, descripcion_perfil as descripcion_perfil2
            from VW_PERFILES
        ";

        $where = " WHERE id_perfil > 0 and estado_perfil != 'X' ";

        $selectTotalRegistros = "
            select count(*) as total_registros
            from VW_PERFILES
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
                                foreach ($tmp as $k2 => $v2) {
                                    if ($k2 == 'value') {
                                        $valor = $v2;
                                    }

                                    if ($k == 'estado_perfil' && $valor == 'T') {
                                        continue;
                                    }
                                }

                                $where = $where . " AND " . $k . " = '$valor' ";
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

//        foreach ($records as $key => $val) {
//            echo 'KEY IS: ' . $key . '<br/>';
//            echo 'VALUE IS: ' . $val . '<br/>';
//
//            foreach ($records[$key] as $_key => $_val) {
//                echo '_KEY IS: ' . $_key . '<br/>';
//                echo '_VALUE IS: ' . $_val . '<br/>';
//
//                if (is_array($_val)) {
//                    foreach ($_val as $tmpKey => $tmpVal) {
//                        echo '__KEY IS: ' . $tmpKey . '<br/>';
//                        echo '__VALUE IS: ' . $tmpVal . '<br/>';
//                        $valor = $tmpVal;
//                    }
//                } else {
//                    if ($_key == 'value') {
//                        $valor = $_val;
//                    }
//                }
//
//
//
//                if ($_key == 'matchMode') {
//                    if ($key == 'estado_perfil' && $valor == 'T') {
//                        continue;
//                    } else {
//                        echo 'ppp';
//                        echo $_val;
//                        echo 'ppp';
//                        switch ($_val) {
//                            case 'startsWith':
//                                $where = $where . " AND " . $key . " like '$valor%' ";
//                                break;
//                            case 'contains':
//                                $where = $where . " AND " . $key . " like '%$valor%' ";
//                                break;
//                            case 'equals':
//                                echo 'fdfdfddfd';
//                                $where = $where . " AND " . $key . " = '$valor' ";
//                                break;
//                            case 'in':
//                                echo '999<br>...';
//                                echo $valor;
//                                echo '...<br>999';
//                                //print_r($valor)
//                                echo 'Este es el valor fgfgsf <br>999';
//                                if (is_array($_val)) {
//                                    echo 'Este es el valor <br>999';
//                                    print_r($_val);
//                                    echo 'Este es el valor <br>999';
//                                    echo '<br>';
//                                    $valAux = $_val;
//                                    $cadenaIn = " IN(";
//                                    foreach ($valAux as $valueIn) {
//                                        $cadenaIn = $cadenaIn . "'$valueIn'";
//
//                                        if (next($valor) == true) {
//                                            $cadenaIn = $cadenaIn . ",";
//                                        } else {
//                                            $cadenaIn = $cadenaIn . ") ";
//                                        }
//                                    }
//                                    //echo $cadenaIn;
//                                    $where = $where . " AND " . $key . $cadenaIn;
//                                }
//                                break;
//                        }
//                    }
//                }
//            }
//        }
//        echo '<br>....';
//        echo $valor;
//        foreach ($records as $key => $val) {
//            //echo 'KEY IS:' . $key . '<br/>';
//            $field = $key;
//            foreach ($records[$key] as $_key => $_val) {
//                echo 'KEY IS:' . $_key . '<br/>';
//                //echo 'VALUE IS: ' . $_val . '<br/>';
//
//
//                if ($_key == 'value') {
//                    echo 'if key';
//                    if (is_array($_key)) {
//                        echo 'if2 key';
//                        $tmpRecord = $_key;
//
//                        foreach ($tmpRecord as $tmpKey => $auxVal) {
//                            if ($tmpKey == 'value') {
//                                $valor = $auxVal;
//                            }
//                        }
//                    } else {
//                        $valor = $_val;
//                    }
//                }
//
//                if ($_key == 'matchMode') {
//                    if ($key == 'estado_perfil' && $valor == 'T') {
//                        continue;
//                    } else {
//                        switch ($_val) {
//                            case 'startsWith':
//                                $where = $where . " AND " . $key . " like '$valor%' ";
//                                break;
//                            case 'contains':
//                                $where = $where . " AND " . $key . " like '%$valor%' ";
//                                break;
//                            case 'equals':
//                                $where = $where . " AND " . $key . " = '$valor' ";
//                                break;
//                            case 'in':
//                                if (is_array($valor)) {
//                                    $cadenaIn = " IN(";
//                                    foreach ($valor as $valueIn) {
//                                        $cadenaIn = $cadenaIn . "'$valueIn'";
//
//                                        if (next($valor) == true) {
//                                            $cadenaIn = $cadenaIn . ",";
//                                        } else {
//                                            $cadenaIn = $cadenaIn . ") ";
//                                        }
//                                    }
//                                    //echo $cadenaIn;
//                                    $where = $where . " AND " . $key . $cadenaIn;
//                                }
//                                break;
//                        }
//                    }
//                }
//
//
//
//
////                if ($_key == 'value') {
////                    if ($key == 'estado_perfil' && $_val == 'T') {
////                        continue;
////                    } else {
////                        $where = $where . " AND " . $key . " = '$_val' ";
////                    }
////                }
//            }
//        }


        $start = $parametros['start'];
//        if ($start == 0) {
//            $start = 1;
//        }

        $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
        if ($parametros['sortOrder'] == '-1') {
            $order = $order . ' DESC ';
        }

        //$offset = 'OFFSET ' . (($start - 1) * $parametros['limit']) . ' ROWS ';
        $offset = 'OFFSET ' . ($start) . ' ROWS ';
        $fetch = 'FETCH NEXT ' . $parametros['limit'] . ' ROWS ONLY';


        $queryTotalRegistros = $selectTotalRegistros . $where;
        $query = $select . $where . $order . $offset . $fetch;

//        echo $queryTotalRegistros;
//        echo $query;
//        $query = "
//            EXEC SP_GEN_PERFILES            
//            @in_operacion = 'Q'
//        ";

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

    public function getPerfilesxEstado($estado_perfil) {
        //print_r($parametros);
        //$perfil = json_decode(stripslashes($parametros['perfil']), true);

        $estado_perfil = mssql_real_escape_string($estado_perfil);

        $query = "
            EXEC SP_GEN_PERFILES
            @in_estado_perfil = '$estado_perfil',
            @in_operacion = 'QE'
        ";
        
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function insert($parametros) {
        $perfil = json_decode(stripslashes($parametros['perfil']), true);

        //print_r($perfil);
        //echo $perfil['descripcion_perfil'];
        //echo $perfil['estado_perfil'];

        $descripcion_perfil = mssql_real_escape_string($perfil['descripcion_perfil']);
        $estado_perfil = mssql_real_escape_string($perfil['estado_perfil']);

        $query = "
            EXEC SP_GEN_PERFILES
            @in_descripcion_perfil = '$descripcion_perfil',
            @in_estado_perfil = '$estado_perfil',
            @in_operacion = 'I'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function update($parametros) {
        $perfil = json_decode(stripslashes($parametros['perfil']), true);

        $id_perfil = mssql_real_escape_string($perfil['id_perfil']);
        $descripcion_perfil = mssql_real_escape_string($perfil['descripcion_perfil']);
        $estado_perfil = mssql_real_escape_string($perfil['estado_perfil']);

        $query = "
            EXEC SP_GEN_PERFILES
            @in_id_perfil = '$id_perfil',
            @in_descripcion_perfil = '$descripcion_perfil',
            @in_estado_perfil = '$estado_perfil',
            @in_operacion = 'U'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function delete($parametros) {
        $perfil = json_decode(stripslashes($parametros['perfil']), true);

        $id_perfil = mssql_real_escape_string($perfil['id_perfil']);
        //$descripcion_perfil = mssql_real_escape_string($perfil['descripcion_perfil']);
        //$estado_perfil = mssql_real_escape_string($perfil['estado_perfil']);

        $query = "
            EXEC SP_GEN_PERFILES
            @in_id_perfil = '$id_perfil',
            @in_operacion = 'D'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
