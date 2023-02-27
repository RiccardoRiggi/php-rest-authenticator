<?php

include './importManager.php';
include '../services/dispositivoFisicoService.php';
include '../services/autenticazioneService.php';


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


    if ($_GET["nomeMetodo"] == "generaIdentificativoDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        $response = generaIdentificativoDispositivoFisico();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "isDispositivoAbilitato") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        $response = isDispositivoAbilitato($_GET["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        if (!isset($jsonBody["nomeDispositivo"]))
            throw new OtterGuardianException(400, "Il campo nomeDispositivo è richiesto");

        $response = abilitaDispositivoFisico($jsonBody["idDispositivoFisico"], $jsonBody["nomeDispositivo"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        $response = disabilitaDispositivoFisico($jsonBody["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDispositiviFisici") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");

        $response = getDispositiviFisici($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getRichiesteDiAccessoPendenti") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        $response = getRichiesteDiAccessoPendenti($jsonBody["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "autorizzaAccesso") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        if (!isset($jsonBody["idTwoFact"]))
            throw new OtterGuardianException(400, "Il campo idTwoFact è richiesto");

        autorizzaAccesso($jsonBody["idDispositivoFisico"], $jsonBody["idTwoFact"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "autorizzaQrCode") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new OtterGuardianException(400, "Il campo idDispositivoFisico è richiesto");

        if (!isset($jsonBody["idQrCode"]))
            throw new OtterGuardianException(400, "Il campo idQrCode è richiesto");

        autorizzaQrCode($jsonBody["idDispositivoFisico"], $jsonBody["idQrCode"]);
        http_response_code(200);
    } else {
        throw new ErroreServerException("Metodo non implementato");
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
