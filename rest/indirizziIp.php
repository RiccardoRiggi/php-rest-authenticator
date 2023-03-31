<?php

include './importManager.php';
include '../services/indirizziIpService.php';


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


    if ($_GET["nomeMetodo"] == "getIndirizziIp") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getIndirizziIp($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "sbloccaIndirizzoIp") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["indirizzoIp"]))
            throw new OtterGuardianException(400, "Il campo indirizzoIp è richiesto");

        $response = sbloccaIndirizzoIp($jsonBody["indirizzoIp"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "bloccaIndirizzoIp") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["indirizzoIp"]))
            throw new OtterGuardianException(400, "Il campo indirizzoIp è richiesto");

        $response = bloccaIndirizzoIpLista($jsonBody["indirizzoIp"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "azzeraContatoreAlert") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["indirizzoIp"]))
            throw new OtterGuardianException(400, "Il campo indirizzoIp è richiesto");

        $response = azzeraContatoreAlert($jsonBody["indirizzoIp"]);
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
