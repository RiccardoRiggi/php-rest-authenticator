<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVociMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getVociMenu')) {
    function getVociMenu($pagina)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT f.idVoceMenu, f.idVoceMenuPadre, f.descrizione, (SELECT p.descrizione FROM " . PREFISSO_TAVOLA . "_voci_menu p WHERE p.idVoceMenu = f.idVoceMenuPadre and p.dataEliminazione IS NULL) as descrizionePadre, f.path, f.icona, f.ordine  FROM " . PREFISSO_TAVOLA . "_voci_menu f WHERE f.dataEliminazione IS NULL ORDER BY f.idVoceMenuPadre, f.idVoceMenu LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

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

        $result = getVociMenuRadice();
        return getVociMenuDatoPadre($result);
    }
}

if (!function_exists('getVociMenuRadice')) {
    function getVociMenuRadice()
    {

        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $sql = "SELECT DISTINCT vm.idVoceMenu, vm.idVoceMenuPadre, vm.descrizione, vm.path, vm.icona, vm.ordine, vm.visibile  FROM " . PREFISSO_TAVOLA . "_voci_menu vm JOIN " . PREFISSO_TAVOLA . "_ruoli_voci_menu rvm ON vm.idVoceMenu = rvm.idVoceMenu JOIN " . PREFISSO_TAVOLA . "_ruoli_utenti ru ON rvm.idTipoRuolo = ru.idTipoRuolo WHERE vm.idVoceMenuPadre IS NULL AND ru.idUtente = :idUtente AND vm.dataEliminazione IS NULL ORDER BY ORDINE ";


        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}


if (!function_exists('getVociMenuDatoPadre')) {
    function getVociMenuDatoPadre($result)
    {

        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);


        $array = [];
        foreach ($result as $value) {
            $sql = "SELECT DISTINCT vm.idVoceMenu, vm.idVoceMenuPadre, vm.descrizione, vm.path, vm.icona, vm.ordine, vm.visibile  FROM " . PREFISSO_TAVOLA . "_voci_menu vm JOIN " . PREFISSO_TAVOLA . "_ruoli_voci_menu rvm ON vm.idvocemenu = rvm.idvocemenu JOIN " . PREFISSO_TAVOLA . "_ruoli_utenti ru ON rvm.idTipoRuolo = ru.idTipoRuolo WHERE ru.idUtente = :idUtente AND idVoceMenuPadre = :idVoceMenuPadre AND vm.dataEliminazione IS NULL ORDER BY ORDINE ";
            $conn = apriConnessione();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->bindParam(':idVoceMenuPadre', $value["idVoceMenu"]);

            $stmt->execute();
            $result = $stmt->fetchAll();
            chiudiConnessione($conn);

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
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_voci_menu(idVoceMenuPadre, descrizione, path, icona, ordine, dataCreazione, visibile) VALUES (:idVoceMenuPadre, :descrizione , :path, :icona, :ordine ,current_timestamp, true)";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenuPadre', $idVoceMenuPadre);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':icona', $icona);
        $stmt->bindParam(':ordine', $ordine);
        $stmt->execute();
        $id = $conn->lastInsertId();
        chiudiConnessione($conn);

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
        verificaValiditaToken();

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
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Modifica della voce di menu con identificativo " . $idVoceMenu);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaVoceMenu')) {
    function eliminaVoceMenu($idVoceMenu)
    {
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_voci_menu SET dataEliminazione = current_timestamp WHERE idVoceMenu = :idVoceMenu AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();
        chiudiConnessione($conn);

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una voce di menu non esistente. Identificativo inserito: " . $idVoceMenu);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
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
        verificaValiditaToken();

        $sql = "SELECT f.idVoceMenu, f.idVoceMenuPadre, f.descrizione, (SELECT p.descrizione FROM " . PREFISSO_TAVOLA . "_voci_menu p WHERE p.idVoceMenu = f.idVoceMenuPadre and p.dataEliminazione IS NULL) as descrizionePadre, f.path, f.icona, f.ordine  FROM " . PREFISSO_TAVOLA . "_voci_menu f WHERE f.dataEliminazione IS NULL AND f.idVoceMenu = :idVoceMenu ";

        generaLogSuBaseDati("DEBUG", $sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();
        $result = $stmt->fetch();
        chiudiConnessione($conn);
        return $result;
    }
}
