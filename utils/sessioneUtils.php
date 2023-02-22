<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: verificaValiditaSessione
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('verificaValiditaSessione')) {
    function verificaValiditaSessione()
    {

        $idSessione = $_SERVER["HTTP_SESSIONE"];
        $impronta = null;
        if (IMPRONTE_SESSIONE_ABILITATE) {
            $impronta = $_SERVER["HTTP_IMPRONTA"];
        }

        $idUtente = getIdUtenteDaSessione($idSessione);

        if (IMPRONTE_SESSIONE_ABILITATE) {
            $validita = verificaValiditaImpronta($idSessione, $impronta);

            if (!$validita) {
                invalidaSessioni($idUtente);
                throw new AccessoNonAutorizzatoLoginException();
            }
        }

        verificaValiditaUtente($idUtente);
        aggiornaDataUltimoUtilizzo($idSessione);

        header('SESSIONE: ' . $idSessione);
       
        
        if (IMPRONTE_SESSIONE_ABILITATE) {
            invalidaImprontePrecedenti($idSessione);
            $nuovaImpronta = registraImprontaSessione($idSessione);
            header('IMPRONTA: ' . $nuovaImpronta);
        }
    }
}

if (!function_exists('getIdUtenteDaSessione')) {
    function getIdUtenteDaSessione($idSessione)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_sessioni WHERE idSessione = :idSessione AND dataFineValidita IS NULL and indirizzoIp = :indirizzoIp AND userAgent = :userAgent ");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return  $result[0]["idUtente"];
    }
}

if (!function_exists('verificaValiditaImpronta')) {
    function verificaValiditaImpronta($idSessione, $impronta)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idImpronta FROM " . PREFISSO_TAVOLA . "_sessioni_impronte WHERE idSessione = :idSessione AND dataUtilizzo IS NULL ORDER BY dataGenerazione DESC LIMIT 1");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return  $result[0]["idImpronta"] == $impronta;
    }
}

if (!function_exists('invalidaSessioni')) {
    function invalidaSessioni($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_sessioni SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente ");
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

if (!function_exists('invalidaImprontePrecedenti')) {
    function invalidaImprontePrecedenti($idSessione)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_sessioni_impronte SET dataUtilizzo = current_timestamp WHERE idSessione = :idSessione AND dataUtilizzo IS NULL");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->execute();
    }
}

if (!function_exists('registraImprontaSessione')) {
    function registraImprontaSessione($idSessione)
    {
        $idImpronta = generaUUID();

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_sessioni_impronte(idSessione, idImpronta, dataGenerazione) VALUES (:idSessione, :idImpronta, current_timestamp)");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->bindParam(':idImpronta', $idImpronta);
        $stmt->execute();

        return $idImpronta;
    }
}

if (!function_exists('aggiornaDataUltimoUtilizzo')) {
    function aggiornaDataUltimoUtilizzo($idSessione)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_sessioni SET dataUltimoUtilizzo = current_timestamp WHERE idSessione = :idSessione ");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->execute();
    }
}