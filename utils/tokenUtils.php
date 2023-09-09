<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: verificaValiditaToken
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('verificaValiditaToken')) {
    function verificaValiditaToken()
    {

        if (ABILITA_VERIFICA_TOKEN) {
            if (!isset($_SERVER["HTTP_TOKEN"])) {
                throw new AccessoNonAutorizzatoLoginException();
            }
            $token = $_SERVER["HTTP_TOKEN"];
            $idUtente = getIdUtenteDaToken($token);
            verificaValiditaUtente($idUtente);
            aggiornaDataUltimoUtilizzo($token);
            verificaAbilitazioneRisorsa($idUtente);
            registraInvocazioneRisorsa();
        }
    }
}

if (!function_exists('getIdUtenteDaToken')) {
    function getIdUtenteDaToken($token)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();

        $sql = "SELECT idUtente FROM " . PREFISSO_TAVOLA . "_token WHERE token = :token AND dataFineValidita IS NULL ";

        if (ABILITA_VERIFICA_STESSO_INDIRIZZO_IP) {
            $sql = $sql . " AND indirizzoIp = :indirizzoIp ";
        }

        if (ABILITA_VERIFICA_STESSO_USER_AGENT) {
            $sql = $sql . " AND userAgent = :userAgent ";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);

        if (ABILITA_VERIFICA_STESSO_INDIRIZZO_IP) {
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        }

        if (ABILITA_VERIFICA_STESSO_USER_AGENT) {
            $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

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
        chiudiConnessione($conn);
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
        chiudiConnessione($conn);

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
        chiudiConnessione($conn);
    }
}

if (!function_exists('verificaAbilitazioneRisorsa')) {
    function verificaAbilitazioneRisorsa($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT r.idRisorsa FROM " . PREFISSO_TAVOLA . "_risorse r JOIN " . PREFISSO_TAVOLA . "_ruoli_risorse rr ON r.idRisorsa = rr.idRisorsa JOIN " . PREFISSO_TAVOLA . "_ruoli_utenti ru ON rr.idTipoRuolo = ru.idTipoRuolo WHERE ru.idUtente = :idUtente AND r.nomeMetodo = :nomeMetodo AND r.dataEliminazione IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':nomeMetodo', $_GET["nomeMetodo"]);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) < 1) {
            throw new OtterGuardianException(403, "L'utente non è abilitato alla risorsa richiesta");
        }
        return $result;
    }
}

if (!function_exists('registraInvocazioneRisorsa')) {
    function registraInvocazioneRisorsa()
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $endpoint = $_SERVER["REQUEST_URI"];

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_log_chiamate (endpoint, indirizzoIp, nomeMetodo, token) VALUES (:endpoint, :indirizzoIp, :nomeMetodo, :token)");
        $stmt->bindParam(':endpoint', $endpoint);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':nomeMetodo', $_GET["nomeMetodo"]);
        $stmt->bindParam(':token', $_SERVER["HTTP_TOKEN"]);


        $stmt->execute();
        chiudiConnessione($conn);
    }
}
