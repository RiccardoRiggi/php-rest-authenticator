<?php

if (!function_exists('httpAccessoNonAutorizzato')) {
    function httpAccessoNonAutorizzato()
    {
        http_response_code(401);
        $oggetto = new stdClass();
        $oggetto->codice = 401;
        $oggetto->descrizione = "Accesso non autorizzato";
        exit(json_encode($oggetto));
    }
}

if (!function_exists('httpAccessoNonAutorizzatoLogin')) {
    function httpAccessoNonAutorizzatoLogin()
    {
        http_response_code(401);
        $oggetto = new stdClass();
        $oggetto->codice = 401;
        $oggetto->descrizione = "Credenziali errate";
        exit(json_encode($oggetto));
    }
}

if (!function_exists('httpAccessoNegato')) {
    function httpAccessoNegato()
    {
        http_response_code(403);
        $oggetto = new stdClass();
        $oggetto->codice = 403;
        $oggetto->descrizione = "Accesso negato";
        exit(json_encode($oggetto));
    }
}

if (!function_exists('httpMetodoHttpErrato')) {
    function httpMetodoHttpErrato()
    {
        http_response_code(405);
        $oggetto = new stdClass();
        $oggetto->codice = 405;
        $oggetto->descrizione = "Metodo HTTP non supportato";
        exit(json_encode($oggetto));
    }
}

if (!function_exists('httpErroreServer')) {
    function httpErroreServer($descrizione)
    {
        http_response_code(500);
        $oggetto = new stdClass();
        $oggetto->codice = 500;
        $oggetto->descrizione = $descrizione;
        exit(json_encode($oggetto));
    }
}
