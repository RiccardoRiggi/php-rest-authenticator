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

if (!function_exists('inserisciIndirizzoIp')) {
    function inserisciIndirizzoIp()
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());

        try {
            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_indirizzi_ip (indirizzoIp) VALUES (:indirizzoIp)");
            $stmt->bindParam(':indirizzoIp', $indirizzoIp);

            $stmt->execute();
            chiudiConnessione($conn);

        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione generaLogSuBaseDati: " . $e->getMessage());
        }
    }
}

if (!function_exists('bloccaIndirizzoIp')) {
    function bloccaIndirizzoIp()
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET dataBlocco = current_timestamp WHERE indirizzoIp = :indirizzoIp";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('verificaIndirizzoIp')) {
    function verificaIndirizzoIp()
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT indirizzoIp, contatoreAlert, dataBlocco FROM " . PREFISSO_TAVOLA . "_indirizzi_ip WHERE indirizzoIp = :indirizzoIp");
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) < 1) {
            inserisciIndirizzoIp();
        } else {
            if ($result[0]["dataBlocco"] != null) {
                throw new OtterGuardianException(401, "Indirizzo ip bloccato dal sistema");
            } else if ($result[0]["contatoreAlert"] > 10) {
                bloccaIndirizzoIp();
                throw new OtterGuardianException(401, "Indirizzo ip bloccato dal sistema");
            }
        }

    }
}

if (!function_exists('bloccaIndirizzoIp')) {
    function bloccaIndirizzoIp()
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET dataBlocco = current_timestamp WHERE indirizzoIp = :indirizzoIp";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('incrementaContatoreAlert')) {
    function incrementaContatoreAlert()
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $sql = "UPDATE " . PREFISSO_TAVOLA . "_indirizzi_ip SET contatoreAlert = contatoreAlert + 1 WHERE indirizzoIp = :indirizzoIp";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}



