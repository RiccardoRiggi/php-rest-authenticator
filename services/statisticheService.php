<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getStatisticheMetodi
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getStatisticheMetodi')) {
    function getStatisticheMetodi()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as chiamate, nomeMetodo FROM " . PREFISSO_TAVOLA . "_log_chiamate group by nomeMetodo order by chiamate desc";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroDispositiviFisiciAttivi
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroDispositiviFisiciAttivi')) {
    function getNumeroDispositiviFisiciAttivi()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_dispositivi_fisici WHERE dataAbilitazione IS NOT NULL AND dataDisabilitazione IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroIndirizziIp
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroIndirizziIp')) {
    function getNumeroIndirizziIp()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_indirizzi_ip";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroLogin
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroLogin')) {
    function getNumeroLogin()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_login WHERE token IS NOT NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroRisorse
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroRisorse')) {
    function getNumeroRisorse()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_risorse WHERE dataEliminazione IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroAccessiAttivi
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroAccessiAttivi')) {
    function getNumeroAccessiAttivi()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_token WHERE dataFineValidita IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroUtenti
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroUtenti')) {
    function getNumeroUtenti()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_utenti WHERE dataEliminazione IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroRuoli
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroRuoli')) {
    function getNumeroRuoli()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_t_ruoli";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNumeroVociMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNumeroVociMenu')) {
    function getNumeroVociMenu()
    {
        verificaValiditaToken();

        $sql = "SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_voci_menu WHERE dataEliminazione IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0];
    }
}