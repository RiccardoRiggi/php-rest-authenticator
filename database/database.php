<?php

if (!function_exists('apriConnessione')) {
    function apriConnessione()
    {
        $conn = new PDO("mysql:host=" . HOST_DATABASE . ";dbname=" . NOME_DATABASE, USERNAME_DATABASE, PASSWORD_DATABASE);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("set names utf8mb4");
        return $conn;
    }
}

if (!function_exists('chiudiConnessione')) {
    function chiudiConnessione($conn)
    {
        $conn = null;
    }
}

if (!function_exists('restituisciOggettoFiltrato')) {
    function restituisciOggettoFiltrato($result, $array)
    {

        if (is_array($result)) {
            $arrayFinale = [];
            foreach ($result as $row) {
                if (is_array($row)) {
                    $tmp = null;
                    foreach ($array as $value) {
                        $tmp[$value] = $row[$value];
                    }
                    array_push($arrayFinale, $tmp);
                } else {
                    foreach ($array as $value) {
                        $tmp[$value] = $result[$value];
                    }
                    return $tmp;
                }
            }
            return $arrayFinale;
        } else {
            return $result;
        }
    }
}
