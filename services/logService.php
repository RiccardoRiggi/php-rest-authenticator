<?php


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getLogs
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getLogs')) {
    function getLogs($pagina, $logLevel)
    {
        verificaValiditaToken();

        $logLevelIn = "";

        if("ERROR"==$logLevel){
            $logLevelIn="'ERROR'";
        }else if("WARN"==$logLevel){
            $logLevelIn="'ERROR','WARN'";
        }else if("INFO"==$logLevel){
            $logLevelIn="'ERROR','WARN','INFO'";
        }else if("DEBUG"==$logLevel){
            $logLevelIn="'ERROR','WARN','INFO','DEBUG'";
        }
        
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT dataEvento, logLevel, testo, path, indirizzoIp, metodoHttp FROM " . PREFISSO_TAVOLA . "_log WHERE logLevel IN (".$logLevelIn.") ORDER BY dataEvento DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["indirizzoIp"] = decifraStringa($value["indirizzoIp"]);
            $tmp["4"] = decifraStringa($value["4"]);
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getLogsTelegram
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getLogsTelegram')) {
    function getLogsTelegram($pagina)
    {
        verificaValiditaToken();

       
        
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT dataEvento, jsonBody FROM " . PREFISSO_TAVOLA . "_log_telegram ORDER BY dataEvento DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNotificheTelegram
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNotificheTelegram')) {
    function getNotificheTelegram($pagina)
    {
        verificaValiditaToken();

       
        
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT dataInvio, testo FROM " . PREFISSO_TAVOLA . "_notifiche_telegram ORDER BY dataInvio DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}