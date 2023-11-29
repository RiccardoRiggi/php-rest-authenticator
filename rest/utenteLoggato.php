<?php

include './importManager.php';
include '../services/utenteLoggatoService.php';


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


    if ($_GET["nomeMetodo"] == "getUtenteLoggato") {

        verificaMetodoHttp("GET");


        $response = getUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "generaCodiciBackup") {

        verificaMetodoHttp("GET");


        $response = generaCodiciBackup();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "verificaAutenticazione") {

        verificaMetodoHttp("GET");


        verificaAutenticazione();
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "invalidaToken") {

        verificaMetodoHttp("PUT");


        invalidaTokenSpecifico();
        http_response_code(200);
        exit(json_encode($response));
    } elseif ($_GET["nomeMetodo"] == "getStoricoAccessi") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");

        $response = getStoricoAccessi($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiAutenticazionePerUtenteLoggato") {

        verificaMetodoHttp("GET");


        $response = getMetodiAutenticazionePerUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaTipoMetodoLogin") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoMetodoLogin");


        $response = abilitaTipoMetodoLogin($_GET["idTipoMetodoLogin"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaTipoMetodoLogin") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoMetodoLogin");


        $response = disabilitaTipoMetodoLogin($_GET["idTipoMetodoLogin"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiRecuperoPasswordPerUtenteLoggato") {

        verificaMetodoHttp("GET");


        $response = getMetodiRecuperoPasswordPerUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaTipoRecuperoPassword") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoMetodoRecPsw ");


        $response = abilitaTipoRecuperoPassword($_GET["idTipoMetodoRecPsw"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaTipoRecuperoPassword") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoMetodoRecPsw ");


        $response = disabilitaTipoRecuperoPassword($_GET["idTipoMetodoRecPsw"]);
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
