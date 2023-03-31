<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getIndirizziIp
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
        return $result;
    }
}
