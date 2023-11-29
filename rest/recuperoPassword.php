<?php

include './importManager.php';
include '../services/recuperoPasswordService.php';

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


    if ($_GET["nomeMetodo"] == "getMetodiRecuperoPasswordSupportati") {

        verificaMetodoHttp("POST");

        

        verificaParametroJsonBody("email");

        $response = getMetodiRecuperoPasswordSupportati(getParametroJsonBody("email"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "effettuaRichiestaRecuperoPassword") {


        verificaMetodoHttp("POST");



        

        verificaParametroJsonBody("email");
        

        verificaParametroJsonBody("tipoRecuperoPassword");



        $response = effettuaRichiestaRecuperoPassword(getParametroJsonBody("email"), getParametroJsonBody("tipoRecuperoPassword"));
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "confermaRecuperoPassword") {


        verificaMetodoHttp("POST");



        

        verificaParametroJsonBody("idRecPsw");

        verificaParametroJsonBody("codice");

        verificaParametroJsonBody("nuovaPassowrd");

        verificaParametroJsonBody("confermaNuovaPassword");

        if (getParametroJsonBody("nuovaPassowrd") != getParametroJsonBody("confermaNuovaPassword"))
            throw new OtterGuardianException(400, "Le password non corrispondono");




        confermaRecuperoPassword(getParametroJsonBody("idRecPsw"), getParametroJsonBody("codice"), getParametroJsonBody("nuovaPassowrd"));
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
