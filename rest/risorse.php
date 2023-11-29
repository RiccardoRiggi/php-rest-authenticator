<?php

include './importManager.php';
include '../services/risorseService.php';


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


    if ($_GET["nomeMetodo"] == "getRisorse") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");


        $response = getRisorse($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciRisorsa") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("idRisorsa");

        verificaParametroJsonBody("descrizione");

        verificaParametroJsonBody("nomeMetodo");

        if (str_starts_with(getParametroJsonBody("idRisorsa"), "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la creazione di nuove risorse");
        }

        if (str_starts_with(getParametroJsonBody("idRisorsa"), "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la creazione di nuove risorse");
        }


        inserisciRisorsa(getParametroJsonBody("idRisorsa"), getParametroJsonBody("nomeMetodo"), getParametroJsonBody("descrizione"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaRisorsa") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idRisorsa");


        

        verificaParametroJsonBody("descrizione");

        verificaParametroJsonBody("nomeMetodo");

        if (str_starts_with($_GET["idRisorsa"], "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la modifica di una risorsa");
        }

        if (str_starts_with($_GET["idRisorsa"], "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la modifica di una risorsa");
        }


        $response = modificaRisorsa(getParametroJsonBody("nomeMetodo"), getParametroJsonBody("descrizione"), $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaRisorsa") {

        verificaMetodoHttp("DELETE");

        verificaParametroGet("idRisorsa");

        if (str_starts_with($_GET["idRisorsa"], "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per l'eliminazione di una risorsa");
        }

        if (str_starts_with($_GET["idRisorsa"], "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per l'eliminazione di una risorsa");
        }


        eliminaRisorsa($_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRisorsa") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idRisorsa");


        $response = getRisorsa($_GET["idRisorsa"]);
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
