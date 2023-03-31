<?php

include './importManager.php';
include '../services/utentiService.php';


try {

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Expose-Headers: *');
    header('Access-Control-Max-Age: 86400');
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
        exit();

    verificaIndirizzoIp();

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getListaUtenti") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getListaUtenti($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["nome"]))
            throw new OtterGuardianException(400, "Il campo nome è richiesto");

        if (!isset($jsonBody["cognome"]))
            throw new OtterGuardianException(400, "Il campo cognome è richiesto");

        if (!isset($jsonBody["email"]))
            throw new OtterGuardianException(400, "Il campo email è richiesto");

        if (!isset($jsonBody["password"]))
            throw new OtterGuardianException(400, "Il campo password è richiesto");

        if (!isset($jsonBody["confermaPassword"]))
            throw new OtterGuardianException(400, "Il campo confermaPassword è richiesto");

        if ($jsonBody["confermaPassword"] !== $jsonBody["password"]) {
            throw new OtterGuardianException(400, "Il campo password deve essere uguale al campo confermaPassword");
        }

        inserisciUtente($jsonBody["nome"], $jsonBody["cognome"], $jsonBody["email"], $jsonBody["password"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["nome"]))
            throw new OtterGuardianException(400, "Il campo nome è richiesto");

        if (!isset($jsonBody["cognome"]))
            throw new OtterGuardianException(400, "Il campo cognome è richiesto");


        $response = modificaUtente($jsonBody["nome"], $jsonBody["cognome"], $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "DELETE")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");



        eliminaUtente($_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        $response = getUtente($_GET["idUtente"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "bloccaUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        bloccaUtente($_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "sbloccaUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");

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
