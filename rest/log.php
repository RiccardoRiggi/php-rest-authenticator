<?php

include './importManager.php';
include '../services/logService.php';


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

    if ($_GET["nomeMetodo"] == "getLogs") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");
        verificaParametroGet("livelloLog");

        $response = getLogs($_GET["pagina"], $_GET["livelloLog"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getLogsTelegram") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");

        $response = getLogsTelegram($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNotificheTelegram") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");

        $response = getNotificheTelegram($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else {
        throw new ErroreServerException("Metodo non implementato");
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
