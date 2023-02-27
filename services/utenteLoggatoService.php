<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getUtenteLoggato
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('getUtenteLoggato')) {
    function getUtenteLoggato()
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);
        $utente = getUtenteDecifrato($idUtente);

        $oggetto = new stdClass();
        $oggetto->idUtente = $utente[0]["idUtente"];
        $oggetto->nome = decifraStringa($utente[0]["nome"]);
        $oggetto->cognome = decifraStringa($utente[0]["cognome"]);
        $oggetto->email = decifraStringa($utente[0]["email"]);
        $oggetto->dataCreazione = $utente[0]["dataCreazione"];
        $oggetto->dataUltimaModifica = $utente[0]["dataUltimaModifica"];
        return $oggetto;
    }
}

if (!function_exists('getUtenteDecifrato')) {
    function getUtenteDecifrato($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente, nome, cognome, email, dataCreazione, dataUltimaModifica FROM " . PREFISSO_TAVOLA . "_utenti WHERE idUtente = :idUtente AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0)
            throw new OtterGuardianException(404, "Utente non trovato");

        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: invalidaToken
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('invalidaTokenSpecifico')) {
    function invalidaTokenSpecifico()
    {
        verificaValiditaToken();
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = current_timestamp WHERE token = :token ");
        $stmt->bindParam(':token', $_SERVER["HTTP_TOKEN"]);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: invalidaToken
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (!function_exists('verificaAutenticazione')) {
    function verificaAutenticazione()
    {
        verificaValiditaToken();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getStoricoAccessi
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getStoricoAccessi')) {
    function getStoricoAccessi($pagina)
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT dataInizioValidita, dataFineValidita, dataUltimoUtilizzo, indirizzoIp, userAgent  FROM " . PREFISSO_TAVOLA . "_token WHERE idUtente = :idUtente ORDER BY dataInizioValidita DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        generaLogSuFile($sql);
        generaLogSuFile($idUtente);
        generaLogSuFile($paginaDaEstrarre);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["indirizzoIp"] = decifraStringa($value["indirizzoIp"]);
            $tmp["3"] = decifraStringa($value["3"]);
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: generaCodiciBackup
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('generaCodiciBackup')) {
    function generaCodiciBackup()
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);
        eliminaCodiciBackupPresenti($idUtente);

        $array = [];
        while (count($array) < 5) {
            $codiceTmp = generaCodiceSeiCifre() . generaCodiceSeiCifre() . generaCodiceSeiCifre();
            inserisciCodiceBackup($idUtente, $codiceTmp);
            array_push($array, $codiceTmp);
        }

        return $array;
    }
}

if (!function_exists('eliminaCodiciBackupPresenti')) {
    function eliminaCodiciBackupPresenti($idUtente)
    {
        verificaValiditaToken();
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_codici_backup SET dataEliminazione = current_timestamp WHERE idUtente = :idUtente AND dataEliminazione IS NULL and dataUtilizzo IS NULL ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

if (!function_exists('inserisciCodiceBackup')) {
    function inserisciCodiceBackup($idUtente, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_codici_backup (idUtente, codice, dataGenerazione) VALUES (:idUtente, :codice, current_timestamp)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
    }
}
