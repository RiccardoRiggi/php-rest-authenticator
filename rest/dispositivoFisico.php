<?php

include './importManager.php';
include '../services/dispositivoFisicoService.php';
include '../services/autenticazioneService.php';


try {

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "generaIdentificativoDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        $response = generaIdentificativoDispositivoFisico();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new ErroreServerException("Il campo idDispositivoFisico è richiesto");

        if (!isset($jsonBody["nomeDispositivo"]))
            throw new ErroreServerException("Il campo nomeDispositivo è richiesto");

        $response = abilitaDispositivoFisico($jsonBody["idDispositivoFisico"], $jsonBody["nomeDispositivo"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new ErroreServerException("Il campo idDispositivoFisico è richiesto");

        $response = disabilitaDispositivoFisico($jsonBody["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDispositiviFisici") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        $response = getDispositiviFisici();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getRichiesteDiAccessoPendenti") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new ErroreServerException("Il campo idDispositivoFisico è richiesto");

        $response = getRichiesteDiAccessoPendenti($jsonBody["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "autorizzaAccesso") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idDispositivoFisico"]))
            throw new ErroreServerException("Il campo idDispositivoFisico è richiesto");

        if (!isset($jsonBody["idTwoFact"]))
            throw new ErroreServerException("Il campo idTwoFact è richiesto");

        autorizzaAccesso($jsonBody["idDispositivoFisico"], $jsonBody["idTwoFact"]);
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
} catch (Exception $e) {
    generaLogSuFile("Errore sconosciuto: " . $e->getMessage());
    httpErroreServer("Errore sconosciuto");
}
