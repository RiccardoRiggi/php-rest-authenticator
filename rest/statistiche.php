<?php

include './importManager.php';
include '../services/statisticheService.php';


try {
   
    if (ABILITA_CORS) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Expose-Headers: *');
        header('Access-Control-Max-Age: 86400');
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
            exit();
    }

    verificaIndirizzoIp();

    verificaPresenzaNomeMetodo();


    if ($_GET["nomeMetodo"] == "getStatisticheMetodi") {

        verificaMetodoHttp("GET");


        $response = getStatisticheMetodi();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroDispositiviFisiciAttivi") {

        verificaMetodoHttp("GET");


        $response = getNumeroDispositiviFisiciAttivi();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroIndirizziIp") {

        verificaMetodoHttp("GET");


        $response = getNumeroIndirizziIp();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroLogin") {

        verificaMetodoHttp("GET");


        $response = getNumeroLogin();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroRisorse") {

        verificaMetodoHttp("GET");


        $response = getNumeroRisorse();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroAccessiAttivi") {

        verificaMetodoHttp("GET");


        $response = getNumeroAccessiAttivi();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroUtenti") {

        verificaMetodoHttp("GET");


        $response = getNumeroUtenti();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroRuoli") {

        verificaMetodoHttp("GET");


        $response = getNumeroRuoli();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNumeroVociMenu") {

        verificaMetodoHttp("GET");


        $response = getNumeroVociMenu();
        http_response_code(200);
        exit(json_encode($response));
    } else {
        throw new OtterGuardianException(500, "Metodo non implementato");
    }
} catch (AccessoNonAutorizzatoLoginException $e) {
    httpAccessoNonAutorizzatoLogin();
} catch (AccessoNonAutorizzatoException $e) {
    httpAccessoNonAutorizzato();
} catch (MetodoHttpErratoException $e) {
    httpMetodoHttpErrato();
} catch (ErroreServerException $e) {
    httpErroreServer($e->getMessage());
} catch (OtterGuardianException $e) {
    http_response_code($e->getStatus());
    $oggetto = new stdClass();
    $oggetto->codice = $e->getStatus();
    $oggetto->descrizione = $e->getMessage();
    exit(json_encode($oggetto));
} catch (Exception $e) {
    generaLogSuFile("Errore sconosciuto: " . $e->getMessage());
    httpErroreServer("Errore sconosciuto");
}
