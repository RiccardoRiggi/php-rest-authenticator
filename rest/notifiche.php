<?php

include './importManager.php';
include '../services/notificheService.php';
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
    verificaPresenzaNomeMetodo();

    if ($_GET["nomeMetodo"] == "getListaNotifiche") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");

        $response = getListaNotifiche($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciNotifica") {

        verificaMetodoHttp("POST");
        verificaParametroJsonBody("titolo");
        verificaParametroJsonBody("testo");

        inserisciNotifica(getParametroJsonBody("titolo"), getParametroJsonBody("testo"));
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaNotifica") {

        verificaMetodoHttp("PUT");
        verificaParametroGet("idNotifica");
        verificaParametroJsonBody("titolo");
        verificaParametroJsonBody("testo");

        $response = modificaNotifica(getParametroJsonBody("titolo"), getParametroJsonBody("testo"), $_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaNotifica") {

        verificaMetodoHttp("DELETE");
        verificaParametroGet("idNotifica");

        eliminaNotifica($_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getNotifica") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idNotifica");

        $response = getNotifica($_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDestinatariNotifica") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idNotifica");
        verificaParametroGet("pagina");

        $response = getDestinatariNotifica($_GET["pagina"], $_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaTutti") {

        verificaMetodoHttp("POST");
        verificaParametroGet("idNotifica");

        $response = inviaNotificaTutti($_GET["idNotifica"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaRuolo") {

        verificaMetodoHttp("POST");
        verificaParametroGet("idNotifica");
        verificaParametroGet("idTipoRuolo");

        $response = inviaNotificaRuolo($_GET["idNotifica"], $_GET["idTipoRuolo"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaUtente") {

        verificaMetodoHttp("POST");
        verificaParametroGet("idNotifica");
        verificaParametroGet("idUtente");

        $response = inviaNotificaUtente($_GET["idNotifica"], $_GET["idUtente"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNotificaLatoUtente") {

        verificaMetodoHttp("GET");
        verificaParametroGet("idNotifica");

        $response = getNotificaLatoUtente($_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNotificheLatoUtente") {

        verificaMetodoHttp("GET");
        verificaParametroGet("pagina");

        $response = getNotificheLatoUtente($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "eliminaNotificaLatoUtente") {

        verificaMetodoHttp("DELETE");
        verificaParametroGet("idNotifica");

        eliminaNotificaLatoUtente($_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "leggiNotificheLatoUtente") {

        verificaMetodoHttp("PUT");

        leggiNotificheLatoUtente();
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
