<?php

include './importManager.php';
include '../services/ruoliService.php';


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


    if ($_GET["nomeMetodo"] == "getRuoli") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");


        $response = getRuoli($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciRuolo") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("idTipoRuolo");

        verificaParametroJsonBody("descrizione");

        if (str_starts_with(getParametroJsonBody("idTipoRuolo"), "AMM")) {
            throw new OtterGuardianException(400, "Non puoi inserire il ruolo AMM");
        }

        if (str_starts_with(getParametroJsonBody("idTipoRuolo"), "USER")) {
            throw new OtterGuardianException(400, "Non puoi inserire il ruolo USER");
        }

        inserisciRuolo(getParametroJsonBody("idTipoRuolo"), getParametroJsonBody("descrizione"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaRuolo") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");


        

        verificaParametroJsonBody("descrizione");



        if (str_starts_with($_GET["idTipoRuolo"], "AMM")) {
            throw new OtterGuardianException(400, "Non puoi modificare il ruolo AMM");
        }

        if (str_starts_with($_GET["idTipoRuolo"], "USER")) {
            throw new OtterGuardianException(400, "Non puoi modificare il ruolo USER");
        }


        $response = modificaRuolo(getParametroJsonBody("descrizione"), $_GET["idTipoRuolo"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaRuolo") {

        verificaMetodoHttp("DELETE");

        verificaParametroGet("idTipoRuolo");

        if (str_starts_with($_GET["idTipoRuolo"], "AMM")) {
            throw new OtterGuardianException(400, "Non puoi eliminare il ruolo AMM");
        }

        if (str_starts_with($_GET["idTipoRuolo"], "USER")) {
            throw new OtterGuardianException(400, "Non puoi eliminare il ruolo USER");
        }

        eliminaRuolo($_GET["idTipoRuolo"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRuolo") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idTipoRuolo");


        $response = getRuolo($_GET["idTipoRuolo"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloUtente") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idUtente");


        associaRuoloUtente($_GET["idTipoRuolo"], $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloUtente") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idUtente");


        dissociaRuoloUtente($_GET["idTipoRuolo"], $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getUtentiPerRuolo") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("pagina");


        $response = getUtentiPerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloRisorsa") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idRisorsa");


        associaRuoloRisorsa($_GET["idTipoRuolo"], $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloRisorsa") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idRisorsa");


        dissociaRuoloRisorsa($_GET["idTipoRuolo"], $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRisorsePerRuolo") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("pagina");


        $response = getRisorsePerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloVoceMenu") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idVoceMenu");


        associaRuoloVoceMenu($_GET["idTipoRuolo"], $_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloVoceMenu") {

        verificaMetodoHttp("PUT");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("idVoceMenu");


        dissociaRuoloVoceMenu($_GET["idTipoRuolo"], $_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getVociMenuPerRuolo") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idTipoRuolo");

        verificaParametroGet("pagina");


        $response = getVociMenuPerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
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
