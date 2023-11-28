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

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getListaNotifiche") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getListaNotifiche($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciNotifica") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["titolo"]))
            throw new OtterGuardianException(400, "Il campo titolo è richiesto");

        if (!isset($jsonBody["testo"]))
            throw new OtterGuardianException(400, "Il campo testo è richiesto");

        inserisciNotifica($jsonBody["titolo"], $jsonBody["testo"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaNotifica") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");


        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["titolo"]))
            throw new OtterGuardianException(400, "Il campo titolo è richiesto");

        if (!isset($jsonBody["testo"]))
            throw new OtterGuardianException(400, "Il campo testo è richiesto");


        $response = modificaNotifica($jsonBody["titolo"], $jsonBody["testo"], $_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaNotifica") {

        if ($_SERVER['REQUEST_METHOD'] != "DELETE")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");

        eliminaNotifica($_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getNotifica") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");


        $response = getNotifica($_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getDestinatariNotifica") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");

        $response = getDestinatariNotifica($_GET["pagina"], $_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaTutti") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");


        $response = inviaNotificaTutti($_GET["idNotifica"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");


        $response = inviaNotificaRuolo($_GET["idNotifica"], $_GET["idTipoRuolo"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inviaNotificaUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        $response = inviaNotificaUtente($_GET["idNotifica"], $_GET["idUtente"], isset($_GET["invioViaTelegram"]));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNotificaLatoUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");


        $response = getNotificaLatoUtente($_GET["idNotifica"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getNotificheLatoUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getNotificheLatoUtente($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "eliminaNotificaLatoUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "DELETE")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idNotifica"]))
            throw new OtterGuardianException(400, "Il campo idNotifica è richiesto");

        eliminaNotificaLatoUtente($_GET["idNotifica"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "leggiNotificheLatoUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

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
