<?php

include './importManager.php';
include '../services/autenticazioneService.php';

try {

    if (!isset($_GET["nomeMetodo"]))
        throw new ErroreServerException("Non è stato fornito il riferimento del metodo da invocare");


    if ($_GET["nomeMetodo"] == "getMedotoAutenticazionePredefinito") {

        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();

        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new ErroreServerException("Il campo email è richiesto");

        $response = getMedotoAutenticazionePredefinito($jsonBody["email"]);
        http_response_code(200);
        exit(json_encode($response));
    } else if ($_GET["nomeMetodo"] == "effettuaAutenticazione") {


        if ($_SERVER['REQUEST_METHOD'] != "POST")
            throw new MetodoHttpErratoException();



        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"]))
            throw new ErroreServerException("Il campo email è richiesto");

        if (!isset($jsonBody["password"]))
            throw new ErroreServerException("Il campo password è richiesto");

        $response = effettuaAutenticazione($jsonBody["email"], $jsonBody["password"], isset($jsonBody["tipoAutenticazione"]) ? $jsonBody["tipoAutenticazione"] : null);
        http_response_code(200);
        exit(json_encode($response));
    }
} catch (AccessoNonAutorizzatoLoginException $e) {
    httpAccessoNonAutorizzatoLogin();
} catch (AccessoNonAutorizzatoException $e) {
    httpAccessoNonAutorizzato();
} catch (MetodoHttpErratoException $e) {
    httpMetodoHttpErrato();
} catch (ErroreServerException $e) {
    httpErroreServer($e->getMessage());
}catch (Exception $e) {
    generaLogSuFile("Errore nella funzione getIstruzioniSecondoFattore: " . $e->getMessage());
    httpErroreServer("Errore sconosciuto");
}
