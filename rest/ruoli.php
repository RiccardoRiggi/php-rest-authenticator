<?php

include './importManager.php';
include '../services/ruoliService.php';

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


    if ($_GET["nomeMetodo"] == "getRuoli") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getRuoli($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "inserisciRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($jsonBody["descrizione"]))
            throw new OtterGuardianException(400, "Il campo descrizione è richiesto");

        if (str_starts_with($jsonBody["idTipoRuolo"], "AMM")) {
            throw new OtterGuardianException(400, "Non puoi inserire il ruolo AMM");
        }

        if (str_starts_with($jsonBody["idTipoRuolo"], "USER")) {
            throw new OtterGuardianException(400, "Non puoi inserire il ruolo USER");
        }

        inserisciRuolo($jsonBody["idTipoRuolo"], $jsonBody["descrizione"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "modificaRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");


        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["descrizione"]))
            throw new OtterGuardianException(400, "Il campo descrizione è richiesto");



        if (str_starts_with($_GET["idTipoRuolo"], "AMM")) {
            throw new OtterGuardianException(400, "Non puoi modificare il ruolo AMM");
        }

        if (str_starts_with($_GET["idTipoRuolo"], "USER")) {
            throw new OtterGuardianException(400, "Non puoi modificare il ruolo USER");
        }


        $response = modificaRuolo($jsonBody["descrizione"], $_GET["idTipoRuolo"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "eliminaRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "DELETE")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (str_starts_with($_GET["idTipoRuolo"], "AMM")) {
            throw new OtterGuardianException(400, "Non puoi eliminare il ruolo AMM");
        }

        if (str_starts_with($_GET["idTipoRuolo"], "USER")) {
            throw new OtterGuardianException(400, "Non puoi eliminare il ruolo USER");
        }

        eliminaRuolo($_GET["idTipoRuolo"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");


        $response = getRuolo($_GET["idTipoRuolo"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        associaRuoloUtente($_GET["idTipoRuolo"], $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloUtente") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idUtente"]))
            throw new OtterGuardianException(400, "Il campo idUtente è richiesto");


        dissociaRuoloUtente($_GET["idTipoRuolo"], $_GET["idUtente"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getUtentiPerRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getUtentiPerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");


        associaRuoloRisorsa($_GET["idTipoRuolo"], $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloRisorsa") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idRisorsa"]))
            throw new OtterGuardianException(400, "Il campo idRisorsa è richiesto");


        dissociaRuoloRisorsa($_GET["idTipoRuolo"], $_GET["idRisorsa"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getRisorsePerRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getRisorsePerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "associaRuoloVoceMenu") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idVoceMenu"]))
            throw new OtterGuardianException(400, "Il campo idVoceMenu è richiesto");


        associaRuoloVoceMenu($_GET["idTipoRuolo"], $_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "dissociaRuoloVoceMenu") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["idVoceMenu"]))
            throw new OtterGuardianException(400, "Il campo idVoceMenu è richiesto");


        dissociaRuoloVoceMenu($_GET["idTipoRuolo"], $_GET["idVoceMenu"]);
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "getVociMenuPerRuolo") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoRuolo"]))
            throw new OtterGuardianException(400, "Il campo idTipoRuolo è richiesto");

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");


        $response = getVociMenuPerRuolo($_GET["idTipoRuolo"], $_GET["pagina"]);
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
