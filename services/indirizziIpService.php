<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getIndirizziIp
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getIndirizziIp')) {
    function getIndirizziIp($pagina)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;


        $sql = "SELECT indirizzoIp, contatoreAlert, dataBlocco FROM " . PREFISSO_TAVOLA . "_indirizzi_ip ORDER BY dataBlocco DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["indirizzoIp"] = decifraStringa($value["indirizzoIp"]);
            $tmp["0"] = decifraStringa($value["0"]);
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: sbloccaIndirizzoIp
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('sbloccaIndirizzoIp')) {
    function sbloccaIndirizzoIp($indirizzoIpChiaro)
    {
        verificaValiditaToken();

        $indirizzoIp = cifraStringa($indirizzoIpChiaro);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET dataBlocco = NULL WHERE indirizzoIp = :indirizzoIp";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: bloccaIndirizzoIpLista
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('bloccaIndirizzoIpLista')) {
    function bloccaIndirizzoIpLista($indirizzoIpChiaro)
    {
        verificaValiditaToken();

        $indirizzoIp = cifraStringa($indirizzoIpChiaro);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET dataBlocco = CURRENT_TIMESTAMP WHERE indirizzoIp = :indirizzoIp";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: azzeraContatoreAlert
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('azzeraContatoreAlert')) {
    function azzeraContatoreAlert($indirizzoIpChiaro)
    {
        verificaValiditaToken();

        $indirizzoIp = cifraStringa($indirizzoIpChiaro);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET dataBlocco = NULL, contatoreAlert = 0 WHERE indirizzoIp = :indirizzoIp";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
    }
}
