<?php

include './importManager.php';
include '../services/recuperoPasswordService.php';

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

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getMetodiRecuperoPasswordSupportati") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400, "Il campo email è richiesto");

        $response = getMetodiRecuperoPasswordSupportati($jsonBody["email"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "effettuaRichiestaRecuperoPassword") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400, "Il campo email è richiesto");

        if (!isset($jsonBody["tipoRecuperoPassword"]))
            throw new OtterGuardianException(400, "Il campo tipoRecuperoPassword è richiesto");



        $response = effettuaRichiestaRecuperoPassword($jsonBody["email"], $jsonBody["tipoRecuperoPassword"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "confermaRecuperoPassword") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idRecPsw"]))
            throw new OtterGuardianException(400, "Il campo idRecPsw è richiesto");

        if (!isset($jsonBody["codice"]))
            throw new OtterGuardianException(400, "Il campo codice è richiesto");

        if (!isset($jsonBody["nuovaPassowrd"]))
            throw new OtterGuardianException(400, "Il campo nuovaPassowrd è richiesto");

        if (!isset($jsonBody["confermaNuovaPassword"]))
            throw new OtterGuardianException(400, "Il campo confermaNuovaPassword è richiesto");

        if ($jsonBody["nuovaPassowrd"] != $jsonBody["confermaNuovaPassword"])
            throw new OtterGuardianException(400, "Le password non corrispondono");




        confermaRecuperoPassword($jsonBody["idRecPsw"], $jsonBody["codice"], $jsonBody["nuovaPassowrd"]);
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
