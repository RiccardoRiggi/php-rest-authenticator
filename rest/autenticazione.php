<?php

include './importManager.php';
include '../services/autenticazioneService.php';

include '../services/webHookTelegramService.php';




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

    if ($_GET["nomeMetodo"] == "getMedotoAutenticazionePredefinito") {

        verificaMetodoHttp("POST");
        verificaParametroJsonBody("email");

        $response = getMedotoAutenticazionePredefinito(getParametroJsonBody("email"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiAutenticazioneSupportati") {

        verificaMetodoHttp("POST");
        verificaParametroJsonBody("email");

        $response = getMetodiAutenticazioneSupportati(getParametroJsonBody("email"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "effettuaAutenticazione") {


        verificaMetodoHttp("POST");
        verificaParametroJsonBody("email");

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["password"])) {
            if (!isset($jsonBody["tipoAutenticazione"]) || str_contains($jsonBody["tipoAutenticazione"], "PSW")) {
                throw new OtterGuardianException(400, "Il campo password è richiesto");
            }
        }

        $response = effettuaAutenticazione(getParametroJsonBody("email"), isset($jsonBody["password"]) ? $jsonBody["password"] : null, isset($jsonBody["tipoAutenticazione"]) ? $jsonBody["tipoAutenticazione"] : null);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "confermaAutenticazione") {

        verificaMetodoHttp("POST");
        verificaParametroJsonBody("idLogin");
        verificaParametroJsonBody("codice");

        confermaAutenticazione(getParametroJsonBody("idLogin"), getParametroJsonBody("codice"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "recuperaTokenDaLogin") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idLogin");

        recuperaTokenDaLogin($_GET["idLogin"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "generaQrCode") {


        verificaMetodoHttp("GET");

        $response = generaQrCode();
        http_response_code(200);
        $oggetto = new stdClass();
        $oggetto->idQrCode = $response;
        exit(json_encode($oggetto));
    } else if ($_GET["nomeMetodo"] == "recuperaTokenDaQrCode") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idQrCode");

        recuperaTokenDaQrCode($_GET["idQrCode"]);
        http_response_code(200);
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
