<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: verificaValiditaToken
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('verificaValiditaToken')) {
    function verificaValiditaToken()
    {
        if (!isset($_SERVER["HTTP_TOKEN"])) {
            throw new AccessoNonAutorizzatoLoginException();
        }
        $token = $_SERVER["HTTP_TOKEN"];
        $idUtente = getIdUtenteDaToken($token);
        verificaValiditaUtente($idUtente);
        aggiornaDataUltimoUtilizzo($token);
        
    }
}

if (!function_exists('getIdUtenteDaToken')) {
    function getIdUtenteDaToken($token)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_token WHERE token = :token AND dataFineValidita IS NULL and indirizzoIp = :indirizzoIp AND userAgent = :userAgent ");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return  $result[0]["idUtente"];
    }
}

if (!function_exists('invalidaToken')) {
    function invalidaToken($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}



if (!function_exists('verificaValiditaUtente')) {
    function verificaValiditaUtente($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_utenti WHERE idUtente = :idUtente AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) == 0)
            throw new AccessoNonAutorizzatoLoginException();

        return $result;
    }
}

if (!function_exists('aggiornaDataUltimoUtilizzo')) {
    function aggiornaDataUltimoUtilizzo($token)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataUltimoUtilizzo = current_timestamp WHERE token = :token ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }
}
