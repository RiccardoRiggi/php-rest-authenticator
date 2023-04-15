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
        if (ABILITA_LOG_FILE)
            file_put_contents("svil.log", date("d/m/Y H:i:s") . " - " . $contenuto . "\n", FILE_APPEND);
    }
}

if (!function_exists('generaLogSuBaseDati')) {
    function generaLogSuBaseDati($logLevel,$testo)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $path = $_SERVER["REQUEST_URI"];

        try {
            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO ".PREFISSO_TAVOLA."_log (logLevel, testo, path, indirizzoIp, metodoHttp) VALUES (:logLevel, :testo, :path, :indirizzoIp, :metodoHttp)");
            $stmt->bindParam(':logLevel', $logLevel);
            $stmt->bindParam(':testo', $testo);
            $stmt->bindParam(':path', $path);
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);
            $stmt->bindParam(':metodoHttp', $_SERVER['REQUEST_METHOD']);

            $stmt->execute();
            chiudiConnessione($conn);
        } catch (Exception $e) {
            generaLogSuFile( "Errore nella funzione generaLogSuBaseDati: " . $e->getMessage());
        }
    }
}

