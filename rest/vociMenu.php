<?php

include './importManager.php';
include '../services/vociMenuService.php';


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


    if ($_GET["nomeMetodo"] == "getVociMenu") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");

        $response = getVociMenu($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciVoceMenu") {

        verificaMetodoHttp("POST");
        verificaParametroJsonBody("descrizione");
        verificaParametroJsonBody("path");
        verificaParametroJsonBody("icona");
        verificaParametroJsonBody("ordine");

        $response = inserisciVoceMenu(getParametroJsonBody("idVoceMenuPadre"), getParametroJsonBody("descrizione"), getParametroJsonBody("path"), getParametroJsonBody("icona"), getParametroJsonBody("ordine"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "modificaVoceMenu") {

        verificaMetodoHttp("PUT");
        verificaParametroGet("idVoceMenu");
        verificaParametroJsonBody("descrizione");
        verificaParametroJsonBody("path");
        verificaParametroJsonBody("icona");
        verificaParametroJsonBody("ordine");

        $response = modificaVoceMenu(getParametroJsonBody("idVoceMenuPadre"), getParametroJsonBody("descrizione"), getParametroJsonBody("path"), getParametroJsonBody("icona"), getParametroJsonBody("ordine"), $_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaVoceMenu") {

        verificaMetodoHttp("DELETE");
        verificaParametroGet("idVoceMenu");

        $response = eliminaVoceMenu($_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getVociMenuPerUtente") {

        verificaMetodoHttp("GET");

        $response = getVociMenuPerUtente();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getVoceMenu") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idVoceMenu");

        $response = getVoceMenu($_GET["idVoceMenu"]);
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
