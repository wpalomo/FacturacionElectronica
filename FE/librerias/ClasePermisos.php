<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'ClaseMenu.php';
include_once 'funciones.php';

/**
 * Description of ClasePermisos
 *
 * @author jpsanchez
 */
class ClasePermisos {

    public function getMenuPerfil($id_perfil) {
        $id_perfil = mssql_real_escape_string($id_perfil);

        $query = "
            EXEC dbo.SP_GEN_PERMISOS
            @in_id_perfil = '$id_perfil',
            @in_operacion = 'QOP'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] == 'N') {
            $tree = array();

            $data = $result['data'];

            foreach ($data as $key => $rows) {
                $leaf = true;
                $partialSelected = false;
                $expanded = false;
                if ($rows['tipo'] == 'P') {
                    $leaf = false;

                    if ($rows['acceso'] == 'S') {
                        $partialSelected = true;
                    }
                }

                $arr = array(
                    'data' => $rows['id_menu'],
                    'label' => $rows['nombre_menu'],
                    'icon' => trim($rows['icono']),
                    'parent' => $rows['id_menu_padre'],
                    'id_menu' => $rows['id_menu'],
                    'id_menu_padre' => $rows['id_menu_padre'],
                    'partialSelected' => $partialSelected,
                    //$rows['acceso'] == 'S' ? true : false,
                    'leaf' => $leaf,
                    'key' => $rows['id_menu'],
//                    'text' => $rows['nombre_menu'],
//                    //'nombre_menu' => $rows['nombre_menu'],
                    'tipo' => $rows['tipo']
//                    'iconCls' => trim($rows['icono']),
//                    //'MN_ICONO' => trim($rows['MN_ICONO']),
//                    //'mn_clase' => trim($rows['mn_clase']),
//                    'routerLink' => trim($rows['ruta']),
//                    'leaf' => $leaf,
//                    'expanded' => $expanded,
                );

                if (($rows['tipo'] == 'P')) {
                    //$arr = $arr + array('children' => array());
                }

                $this->adj_tree($tree, $arr);
            }
            $nodes = $tree[1];

            //$nodes = $this->str_replace_once('"children"', "data", $nodes);
            //$nodes = str_replace("c", "children", $nodes);
            //echo $nodes;
            //return json_encode($nodes);

            $aux = 1;
            $nodes = json_encode($nodes);
            //$nodes = str_replace("children", "data", $nodes, $aux);
            $nodes = $this->str_replace_once("children", "data", $nodes);

            return $nodes;
        }
    }

    public function getMenuPerfilIdMenu($id_perfil) {
        $id_perfil = mssql_real_escape_string($id_perfil);

        $query = "
            EXEC dbo.SP_GEN_PERMISOS
            @in_id_perfil = '$id_perfil',
            @in_operacion = 'QIM'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function updateMenuPerfil($id_perfil, $json) {
        $ob = json_decode($json);

        if ($ob != null) {
            $query = "
                EXEC dbo.SP_GEN_PERMISOS
                @in_id_perfil = '$id_perfil',
                @in_json = '$json',
                @in_operacion = 'UMP'
            ";

            $parametros = array(
                'query' => $query
            );

            $result = ClaseBaseDatos::query($parametros);

            return $result;
        } else {
            return ClaseJson::getMessageJson(false, 'Error en el envio de información en el archivo JSON');
        }
    }

    public function getOpcionesManejanEmpresa() {
        $id_empresa = mssql_real_escape_string($id_empresa);
        $id_perfil = mssql_real_escape_string($id_perfil);

        $query = "
            EXEC dbo.SP_GEN_PERMISOS
            @in_id_empresa = '$id_empresa',
            @in_id_perfil = '$id_usuario',             
            @in_operacion = 'QME'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    private function adj_tree(&$tree, $item) {
        $i = $item['id_menu'];
        $p = $item['id_menu_padre'];
        $tree[$i] = isset($tree[$i]) ? $item + $tree[$i] : $item;

        $tree[$p]['children'][] = &$tree[$i];
    }

    private function str_replace_once($str_pattern, $str_replacement, $string) {

        if (strpos($string, $str_pattern) !== false) {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }

}
