<?php


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getComboVociMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getComboVociMenu')) {
    function getComboVociMenu()
    {
        verificaValiditaToken();

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idVoceMenu, descrizione FROM " . PREFISSO_TAVOLA . "_voci_menu WHERE dataEliminazione IS NULL ORDER BY descrizione");
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}
