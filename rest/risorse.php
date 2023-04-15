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

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getRisorse") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getRisorse($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");

        if (!isset($jsonBody["descrizione"]))
            throw new OtterGuardianException(400, "Il campo descrizione è richiesto");

        if (!isset($jsonBody["nomeMetodo"]))
            throw new OtterGuardianException(400, "Il campo nomeMetodo è richiesto");

        if (str_starts_with($jsonBody["idRisorsa"], "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la creazione di nuove risorse");
        }

        if (str_starts_with($jsonBody["idRisorsa"], "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la creazione di nuove risorse");
        }


        inserisciRisorsa($jsonBody["idRisorsa"], $jsonBody["nomeMetodo"], $jsonBody["descrizione"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");


        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["descrizione"]))
            throw new OtterGuardianException(400, "Il campo descrizione è richiesto");

        if (!isset($jsonBody["nomeMetodo"]))
            throw new OtterGuardianException(400, "Il campo nomeMetodo è richiesto");

        if (str_starts_with($_GET["idRisorsa"], "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la modifica di una risorsa");
        }

        if (str_starts_with($_GET["idRisorsa"], "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per la modifica di una risorsa");
        }


        $response = modificaRisorsa($jsonBody["nomeMetodo"], $jsonBody["descrizione"], $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "DELETE")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");

        if (str_starts_with($_GET["idRisorsa"], "AMM_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per l'eliminazione di una risorsa");
        }

        if (str_starts_with($_GET["idRisorsa"], "USER_")) {
            throw new OtterGuardianException(400, "Il prefisso dell'id risorsa non è utilizzabile per l'eliminazione di una risorsa");
        }


        eliminaRisorsa($_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");


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
