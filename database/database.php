<?php

include '../config.php';

if (!function_exists('apriConnessione')) {
    function apriConnessione()
    {
        $conn = new PDO("mysql:host=" . HOST_DATABASE . ";dbname=" . NOME_DATABASE, USERNAME_DATABASE, PASSWORD_DATABASE);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}

if (!function_exists('chiudiConnessione')) {
    function chiudiConnessione($conn)
    {
        $conn = null;
    }
}
