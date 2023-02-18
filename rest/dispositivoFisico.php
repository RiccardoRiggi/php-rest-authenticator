<?php

include './importManager.php';
include '../services/dispositivoFisicoService.php';

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

        $response = abilitaDispositivoFisico($idDispositivoFisico, $nomeDispositivo);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaDispositivoFisico") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        $response = disabilitaDispositivoFisico($idDispositivoFisico, $nomeDispositivo);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDispositiviFisici") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        $response = getDispositiviFisici();
        http_response_code(200);
        exit(json_encode($response));
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
