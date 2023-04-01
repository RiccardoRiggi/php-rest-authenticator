<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getListaAccessiAttivi
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getListaAccessiAttivi')) {
    function getListaAccessiAttivi($pagina)
    {
        verificaValiditaToken();

        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;


        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT token, dataInizioValidita, dataUltimoUtilizzo, nome, cognome, indirizzoIp, userAgent  FROM " . PREFISSO_TAVOLA . "_token d JOIN " . PREFISSO_TAVOLA . "_utenti u on u.idUtente = d.idUtente  WHERE u.dataBlocco IS NULL and dataEliminazione IS NULL AND dataInizioValidita IS NOT NULL AND dataFineValidita IS NULL ORDER BY dataInizioValidita DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["nome"] = decifraStringa($value["nome"]);
            $tmp["3"] = decifraStringa($value["3"]);
            $tmp["cognome"] = decifraStringa($value["cognome"]);
            $tmp["4"] = decifraStringa($value["4"]);
            $tmp["indirizzoIp"] = decifraStringa($value["indirizzoIp"]);
            $tmp["5"] = decifraStringa($value["5"]);
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: terminaAccesso
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('terminaAccesso')) {
    function terminaAccesso($token)
    {
        verificaValiditaToken();


        $sql = "UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = CURRENT_TIMESTAMP WHERE token = :token";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }
}
