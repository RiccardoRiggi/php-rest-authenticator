<?php

if (!function_exists('getIndirizzoIp')) {
    function getIndirizzoIp()
    {
        //whether ip is from the share internet  
        if (isset($_SERVER['HTTP_CLIENT_IP']) && "" != $_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && "" != $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if (!function_exists('generaLogSuFile')) {
    function generaLogSuFile($contenuto)
    {
        if (true)
            file_put_contents("svil.log", date("d/m/Y H:i:s") . " - " . $contenuto . "\n", FILE_APPEND);
    }
}

if (!function_exists('generaLogAccessi')) {
    function generaLogAccessi()
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        try {
            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO ".PREFISSO_TAVOLA."_log_accessi (indirizzoIp) VALUES (:indirizzoIp)");
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);
            $stmt->execute();
        } catch (Exception $e) {
            generaLogSuFile( "Errore nella funzione generaLogAccessi: " . $e->getMessage());
        }
    }
}

if (!function_exists('generaLogChiamate')) {
    function generaLogChiamate($idSessione,$pathChiamato)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        try {
            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO ".PREFISSO_TAVOLA."_log_chiamate (idSessione,indirizzoIp,pathChiamato) VALUES (:idSessione,:indirizzoIp,:pathChiamato)");
            $stmt->bindParam(':idSessione', $idSessione);
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);
            $stmt->bindParam(':pathChiamato', $pathChiamato);
            $stmt->execute();
        } catch (Exception $e) {
            generaLogSuFile( "Errore nella funzione generaLogAccessi: " . $e->getMessage());
        }
    }
}

if (!function_exists('generaLogOperazioni')) {
    function generaLogOperazioni($idSessione,$operazione)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        try {
            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO ".PREFISSO_TAVOLA."_log_operazioni (idSessione,indirizzoIp,operazione) VALUES (:idSessione,:indirizzoIp,:operazione)");
            $stmt->bindParam(':idSessione', $idSessione);
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);
            $stmt->bindParam(':operazione', $operazione);
            $stmt->execute();
        } catch (Exception $e) {
            generaLogSuFile( "Errore nella funzione generaLogAccessi: " . $e->getMessage());
        }
    }
}
