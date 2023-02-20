<?php

include './importManager.php';
include '../services/autenticazioneService.php';

try {

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getMedotoAutenticazionePredefinito") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400,"Il campo email è richiesto");

        $response = getMedotoAutenticazionePredefinito($jsonBody["email"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiAutenticazioneSupportati") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400,"Il campo email è richiesto");

        $response = getMetodiAutenticazioneSupportati($jsonBody["email"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "effettuaAutenticazione") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400,"Il campo email è richiesto");

        if (!isset($jsonBody["password"])) {
            if (!isset($jsonBody["tipoAutenticazione"]) || str_contains($jsonBody["tipoAutenticazione"], "PSW",)) {
                throw new OtterGuardianException(400,"Il campo password è richiesto");
            }
        }

        $response = effettuaAutenticazione($jsonBody["email"], isset($jsonBody["password"]) ? $jsonBody["password"] : null, isset($jsonBody["tipoAutenticazione"]) ? $jsonBody["tipoAutenticazione"] : null);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "confermaAutenticazione") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idLogin"]))
            throw new OtterGuardianException(400,"Il campo idLogin è richiesto");

        if (!isset($jsonBody["codice"]))
            throw new OtterGuardianException(400,"Il campo codice è richiesto");


        confermaAutenticazione($jsonBody["idLogin"], $jsonBody["codice"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "recuperaSessioneDaLogin") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idLogin"]))
            throw new OtterGuardianException(400,"Il campo idLogin è richiesto");



        recuperaSessioneDaLogin($jsonBody["idLogin"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "generaQrCode") {


        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        $response = generaQrCode();
        http_response_code(200);
        $oggetto = new stdClass();
        $oggetto->idQrCode = $response;
        exit(json_encode($oggetto));
    } else if ($_GET["nomeMetodo"] == "recuperaSessioneDaQrCode") {


        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idQrCode"]))
            throw new OtterGuardianException(400,"Il campo idQrCode è richiesto");

        recuperaSessioneDaQrCode($_GET["idQrCode"]);
        http_response_code(200);
    } else {
        throw new OtterGuardianException(500,"Metodo non implementato");
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
