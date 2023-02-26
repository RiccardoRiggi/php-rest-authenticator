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
            throw new OtterGuardianException(404,"Utente non trovato");

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