<?php

include './importManager.php';
include '../services/vociMenuService.php';

try {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Expose-Headers: *');
    header('Access-Control-Max-Age: 86400');
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
        exit();


    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getVociMenu") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getVociMenu($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getVociMenuPerUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        $response = getVociMenuPerUtente();
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
