<?php

include './importManager.php';
include '../services/dispositivoFisicoService.php';
include '../services/autenticazioneService.php';


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


    if ($_GET["nomeMetodo"] == "generaIdentificativoDispositivoFisico") {

        verificaMetodoHttp("GET");

        $response = generaIdentificativoDispositivoFisico();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "generaIdentificativoTelegram") {

        verificaMetodoHttp("GET");

        $response = generaIdentificativoTelegram();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "isDispositivoAbilitato") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idDispositivoFisico");

        $response = isDispositivoAbilitato($_GET["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "isDispositivoTelegramAbilitato") {

        verificaMetodoHttp("GET");

        verificaParametroGet("idDispositivoFisico");

        $response = isDispositivoTelegramAbilitato($_GET["idDispositivoFisico"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaDispositivoFisico") {

        verificaMetodoHttp("PUT");

        

        verificaParametroJsonBody("idDispositivoFisico");

        verificaParametroJsonBody("nomeDispositivo");

        $response = abilitaDispositivoFisico(getParametroJsonBody("idDispositivoFisico"), getParametroJsonBody("nomeDispositivo"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaDispositivoFisico") {

        verificaMetodoHttp("PUT");

        

        verificaParametroJsonBody("idDispositivoFisico");

        $response = disabilitaDispositivoFisico(getParametroJsonBody("idDispositivoFisico"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDispositiviFisici") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");

        $response = getDispositiviFisici($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDispositiviFisiciTelegram") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");

        $response = getDispositiviFisiciTelegram($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getRichiesteDiAccessoPendenti") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("idDispositivoFisico");

        $response = getRichiesteDiAccessoPendenti(getParametroJsonBody("idDispositivoFisico"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "autorizzaAccesso") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("idDispositivoFisico");

        verificaParametroJsonBody("idTwoFact");

        autorizzaAccesso(getParametroJsonBody("idDispositivoFisico"), getParametroJsonBody("idTwoFact"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "autorizzaQrCode") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("idDispositivoFisico");

        verificaParametroJsonBody("idQrCode");

        autorizzaQrCode(getParametroJsonBody("idDispositivoFisico"), getParametroJsonBody("idQrCode"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getListaDispositiviFisici") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");

        $response = getListaDispositiviFisici($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getListaDispositiviFisiciTelegram") {

        verificaMetodoHttp("GET");

        verificaParametroGet("pagina");

        $response = getListaDispositiviFisiciTelegram($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "rimuoviDispositivoFisico") {

        verificaMetodoHttp("PUT");

        

        verificaParametroJsonBody("idDispositivoFisico");

        $response = rimuoviDispositivoFisico(getParametroJsonBody("idDispositivoFisico"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "rimuoviDispositivoFisicoTelegram") {

        verificaMetodoHttp("PUT");

        

        verificaParametroJsonBody("idDispositivoFisico");

        $response = rimuoviDispositivoFisicoTelegram(getParametroJsonBody("idDispositivoFisico"));
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
