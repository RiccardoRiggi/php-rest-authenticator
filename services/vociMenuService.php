<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVociMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getVociMenu')) {
    function getVociMenu($pagina)
    {
        //verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT f.idVoceMenu, f.idVoceMenuPadre, f.descrizione, (SELECT p.descrizione FROM ".PREFISSO_TAVOLA."_voci_menu p WHERE p.idVoceMenu = f.idVoceMenuPadre) as descrizionePadre, f.path, f.icona, f.ordine  FROM " . PREFISSO_TAVOLA . "_voci_menu f WHERE f.dataEliminazione IS NULL ORDER BY f.idVoceMenuPadre, f.idVoceMenu LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        generaLogSuBaseDati("DEBUG",$sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVociMenuPerUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('getVociMenuPerUtente')) {
    function getVociMenuPerUtente()
    {
        verificaValiditaToken();


        //AGGIUNGERE IL FILTRO PER RUOLO

        $result = getVociMenuRadice(null);
        return getVociMenuDatoPadre($result);
    }
}

if (!function_exists('getVociMenuRadice')) {
    function getVociMenuRadice()
    {

        $sql = "SELECT idVoceMenu, idVoceMenuPadre, descrizione, path, icona, ordine, visibile  FROM " . PREFISSO_TAVOLA . "_voci_menu WHERE idVoceMenuPadre IS NULL AND dataEliminazione IS NULL ORDER BY ORDINE ";


        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}


if (!function_exists('getVociMenuDatoPadre')) {
    function getVociMenuDatoPadre($result)
    {

        $array = [];
        foreach ($result as $value) {

            $sql = "SELECT idVoceMenu, idVoceMenuPadre, descrizione, path, icona, ordine, visibile  FROM " . PREFISSO_TAVOLA . "_voci_menu WHERE idVoceMenuPadre = :idVoceMenuPadre AND dataEliminazione IS NULL ORDER BY ORDINE ";


            $conn = apriConnessione();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idVoceMenuPadre', $value["idVoceMenu"]);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $tmp = $value;
            if(count($result)>0){
                $tmp["figli"] = getVociMenuDatoPadre($result);
            }else{
                $tmp["figli"] = [];
            }
            array_push($array, $tmp);
        }
        return $array;
    }
}
