<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVociMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getVociMenu')) {
    function getVociMenu($pagina)
    {
        //verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT f.idVoceMenu, f.idVoceMenuPadre, f.descrizione, (SELECT p.descrizione FROM " . PREFISSO_TAVOLA . "_voci_menu p WHERE p.idVoceMenu = f.idVoceMenuPadre and p.dataEliminazione IS NULL) as descrizionePadre, f.path, f.icona, f.ordine  FROM " . PREFISSO_TAVOLA . "_voci_menu f WHERE f.dataEliminazione IS NULL ORDER BY f.idVoceMenuPadre, f.idVoceMenu LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

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
            if (count($result) > 0) {
                $tmp["figli"] = getVociMenuDatoPadre($result);
            } else {
                $tmp["figli"] = [];
            }
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inserisciVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inserisciVoceMenu')) {
    function inserisciVoceMenu($idVoceMenuPadre, $descrizione, $path, $icona, $ordine)
    {
        //verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_voci_menu(idVoceMenuPadre, descrizione, path, icona, ordine, dataCreazione) VALUES (:idVoceMenuPadre, :descrizione , :path, :icona, :ordine ,current_timestamp)";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenuPadre', $idVoceMenuPadre);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':icona', $icona);
        $stmt->bindParam(':ordine', $ordine);
        $stmt->execute();
        $id = $conn->lastInsertId();

        generaLogSuBaseDati("DEBUG", "Inserimento nuova voce di menu con identificativo " . $id);
        return $id;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: modificaVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('modificaVoceMenu')) {
    function modificaVoceMenu($idVoceMenuPadre, $descrizione, $path, $icona, $ordine, $idVoceMenu)
    {
        //verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_voci_menu SET idVoceMenuPadre= :idVoceMenuPadre ,descrizione= :descrizione, path= :path, icona= :icona ,ordine= :ordine WHERE idVoceMenu = :idVoceMenu AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenuPadre', $idVoceMenuPadre);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':icona', $icona);
        $stmt->bindParam(':ordine', $ordine);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();

        generaLogSuBaseDati("DEBUG", "Modifica della voce di menu con identificativo " . $idVoceMenu);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaVoceMenu')) {
    function eliminaVoceMenu($idVoceMenu)
    {
        //verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_voci_menu SET dataEliminazione = current_timestamp WHERE idVoceMenu = :idVoceMenu AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();

        if($numeroRecordModificati!=1){
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una voce di menu non esistente. Identificativo inserito: " . $idVoceMenu);
            throw new OtterGuardianException(500,"Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Eliminazione della voce di menu con identificativo " . $idVoceMenu);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getVoceMenu')) {
    function getVoceMenu($idVoceMenu)
    {
        //verificaValiditaToken();

        $sql = "SELECT f.idVoceMenu, f.idVoceMenuPadre, f.descrizione, (SELECT p.descrizione FROM " . PREFISSO_TAVOLA . "_voci_menu p WHERE p.idVoceMenu = f.idVoceMenuPadre and p.dataEliminazione IS NULL) as descrizionePadre, f.path, f.icona, f.ordine  FROM " . PREFISSO_TAVOLA . "_voci_menu f WHERE f.dataEliminazione IS NULL AND f.idVoceMenu = :idVoceMenu ";

        generaLogSuBaseDati("DEBUG", $sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }
}
