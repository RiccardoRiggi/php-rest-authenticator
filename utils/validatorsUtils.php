<?php

if (!function_exists('verificaMetodoHttp')) {
    function verificaMetodoHttp($metodoHttp)
    {
        if ($_SERVER['REQUEST_METHOD'] != $metodoHttp)
            throw new MetodoHttpErratoException();
    }
}

if (!function_exists('verificaParametroGet')) {
    function verificaParametroGet($nomeParametro)
    {
        if (!isset($_GET[$nomeParametro]))
            throw new OtterGuardianException(400, "Il campo " . $nomeParametro . " è richiesto");
    }
}

if (!function_exists('verificaParametroJsonBody')) {
    function verificaParametroJsonBody($nomeParametro)
    {
        $jsonBody = json_decode(file_get_contents('php://input'), true);
        if (!isset($jsonBody[$nomeParametro]))
            throw new OtterGuardianException(400, "Il campo " . $nomeParametro . " è richiesto");
    }
}

if (!function_exists('verificaPresenzaNomeMetodo')) {
    function verificaPresenzaNomeMetodo()
    {
        if (!isset($_GET["nomeMetodo"]))
            throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");
    }
}

if (!function_exists('getParametroJsonBody')) {
    function getParametroJsonBody($nomeParametro)
    {
        $jsonBody = json_decode(file_get_contents('php://input'), true);
        return $jsonBody[$nomeParametro];
    }
}
