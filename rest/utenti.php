<?php

include './importManager.php';
include '../services/utentiService.php';


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


    if ($_GET["nomeMetodo"] == "getListaUtenti") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");


        $response = getListaUtenti($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciUtente") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("nome");

        verificaParametroJsonBody("cognome");

        verificaParametroJsonBody("email");

        verificaParametroJsonBody("password");

        verificaParametroJsonBody("confermaPassword");

        if (getParametroJsonBody("confermaPassword") !== getParametroJsonBody("password")) {
            throw new OtterGuardianException(400, "Il campo password deve essere uguale al campo confermaPassword");
        }

        inserisciUtente(getParametroJsonBody("nome"), getParametroJsonBody("cognome"), getParametroJsonBody("email"), getParametroJsonBody("password"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaUtente") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idUtente");


        

        verificaParametroJsonBody("nome");

        verificaParametroJsonBody("cognome");


        $response = modificaUtente(getParametroJsonBody("nome"), getParametroJsonBody("cognome"), $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaUtente") {

        verificaMetodoHttp("DELETE");

        verificaParametroGet("idUtente");



        eliminaUtente($_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getUtente") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idUtente");


        $response = getUtente($_GET["idUtente"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "bloccaUtente") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idUtente");


        bloccaUtente($_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "sbloccaUtente") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idUtente");

        sbloccaUtente($_GET["idUtente"]);
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
