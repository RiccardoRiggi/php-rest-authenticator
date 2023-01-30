<?php

include './importManager.php';
include '../services/autenticazioneService.php';


if (!isset($_GET["nomeMetodo"])) {
    httpErroreServer("Non è stato fornito il riferimento del metodo da invocare");
} else if ($_GET["nomeMetodo"] == "getMedotoAutenticazionePredefinito") {

    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        httpMetodoHttpErrato();
    } else {
        $jsonBody = json_decode(file_get_contents('php://input'), true);
        getMedotoAutenticazionePredefinito($jsonBody["email"]);
    }
} else if ($_GET["nomeMetodo"] == "effettuaAutenticazione") {

    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        httpMetodoHttpErrato();
    } else {
        $jsonBody = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonBody["email"])) {
            httpErroreServer("Il campo email è richiesto");
        } else if (!isset($jsonBody["password"])) {
            httpErroreServer("Il campo password è richiesto");
        } else {
            effettuaAutenticazione($jsonBody["email"], $jsonBody["password"], isset($jsonBody["tipoAutenticazione"]) ? $jsonBody["tipoAutenticazione"] : null);
        }
    }
}
