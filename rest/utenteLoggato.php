<?php

include './importManager.php';
include '../services/utenteLoggatoService.php';

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


    if ($_GET["nomeMetodo"] == "getUtenteLoggato") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        $response = getUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "generaCodiciBackup") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        $response = generaCodiciBackup();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "verificaAutenticazione") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        verificaAutenticazione();
        http_response_code(200);
    } else if ($_GET["nomeMetodo"] == "invalidaToken") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();


        invalidaTokenSpecifico();
        http_response_code(200);
        exit(json_encode($response));
    } elseif ($_GET["nomeMetodo"] == "getStoricoAccessi") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["pagina"]))
            throw new OtterGuardianException(400, "Il campo pagina è richiesto");

        $response = getStoricoAccessi($_GET["pagina"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiAutenticazionePerUtenteLoggato") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        $response = getMetodiAutenticazionePerUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaTipoMetodoLogin") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoMetodoLogin"]))
            throw new OtterGuardianException(400, "Il campo idTipoMetodoLogin è richiesto");


        $response = abilitaTipoMetodoLogin($_GET["idTipoMetodoLogin"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaTipoMetodoLogin") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoMetodoLogin"]))
            throw new OtterGuardianException(400, "Il campo idTipoMetodoLogin è richiesto");


        $response = disabilitaTipoMetodoLogin($_GET["idTipoMetodoLogin"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "getMetodiRecuperoPasswordPerUtenteLoggato") {

        if ($_SERVER['REQUEST_METHOD'] != "GET")
            throw new MetodoHttpErratoException();


        $response = getMetodiRecuperoPasswordPerUtenteLoggato();
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "abilitaTipoRecuperoPassword") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoMetodoRecPsw"]))
            throw new OtterGuardianException(400, "Il campo idTipoMetodoRecPsw è richiesto");


        $response = abilitaTipoRecuperoPassword($_GET["idTipoMetodoRecPsw"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "disabilitaTipoRecuperoPassword") {

        if ($_SERVER['REQUEST_METHOD'] != "PUT")
            throw new MetodoHttpErratoException();

        if (!isset($_GET["idTipoMetodoRecPsw"]))
            throw new OtterGuardianException(400, "Il campo idTipoMetodoRecPsw è richiesto");


        $response = disabilitaTipoRecuperoPassword($_GET["idTipoMetodoRecPsw"]);
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
