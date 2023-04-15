<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRuoli
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getRuoli')) {
    function getRuoli($pagina)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT idTipoRuolo, descrizione FROM " . PREFISSO_TAVOLA . "_t_ruoli ORDER BY idTipoRuolo LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

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
Funzione: inserisciRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inserisciRuolo')) {
    function inserisciRuolo($idTipoRuolo, $descrizione)
    {
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_t_ruoli (idTipoRuolo, descrizione) VALUES (:idTipoRuolo, :descrizione )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->execute();
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Inserimento nuovo ruolo con identificativo " . $idTipoRuolo);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: modificaRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('modificaRuolo')) {
    function modificaRuolo($descrizione, $idTipoRuolo)
    {
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_t_ruoli SET descrizione= :descrizione WHERE idTipoRuolo = :idTipoRuolo ";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->execute();
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Modifica del ruolo con identificativo " . $idTipoRuolo);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaRuolo')) {
    function eliminaRuolo($idTipoRuolo)
    {
        verificaValiditaToken();

        $sql = "DELETE FROM " . PREFISSO_TAVOLA . "_t_ruoli WHERE idTipoRuolo = :idTipoRuolo ";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();
        chiudiConnessione($conn);

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una voce di menu non esistente. Identificativo inserito: " . $idTipoRuolo);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Eliminazione della voce di menu con identificativo " . $idTipoRuolo);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getRuolo')) {
    function getRuolo($idTipoRuolo)
    {
        verificaValiditaToken();

        $sql = "SELECT idTipoRuolo, descrizione FROM " . PREFISSO_TAVOLA . "_t_ruoli WHERE idTipoRuolo = :idTipoRuolo ";


        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->execute();
        $result = $stmt->fetch();
        chiudiConnessione($conn);
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: associaRuoloUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('associaRuoloUtente')) {
    function associaRuoloUtente($idTipoRuolo, $idUtente)
    {
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_ruoli_utenti (idTipoRuolo, idUtente, dataCreazione) VALUES (:idTipoRuolo, :idUtente, current_timestamp )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Associato ruolo " . $idTipoRuolo . " all'utente " . $idUtente);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('dissociaRuoloUtente')) {
    function dissociaRuoloUtente($idTipoRuolo, $idUtente)
    {
        verificaValiditaToken();

        $sql = "DELETE FROM " . PREFISSO_TAVOLA . "_ruoli_utenti WHERE idTipoRuolo = :idTipoRuolo AND  idUtente = :idUtente";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();
        chiudiConnessione($conn);

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Dissociato ruolo " . $idTipoRuolo . " dall'utente " . $idUtente);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Dissociato ruolo " . $idTipoRuolo . " dall'utente " . $idUtente);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getUtentiPerRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('getUtentiPerRuolo')) {
    function getUtentiPerRuolo($idTipoRuolo, $pagina)
    {

        verificaValiditaToken();


        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT u.idUtente, u.nome, u.cognome, u.email, r.dataCreazione, r.idTipoRuolo  FROM " . PREFISSO_TAVOLA . "_utenti u LEFT JOIN " . PREFISSO_TAVOLA . "_ruoli_utenti r ON u.idUtente = r.idUtente AND (idTipoRuolo=:idTipoRuolo OR idTipoRuolo IS NULL) WHERE u.dataEliminazione IS NULL AND u.dataBlocco IS NULL ORDER BY u.idUtente LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        generaLogSuFile($sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["nome"] = decifraStringa($value["nome"]);
            $tmp["cognome"] = decifraStringa($value["cognome"]);
            $tmp["email"] = decifraStringa($value["email"]);


            $tmp["1"] = decifraStringa($value["1"]);
            $tmp["2"] = decifraStringa($value["2"]);
            $tmp["3"] = decifraStringa($value["3"]);

            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: associaRuoloRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('associaRuoloRisorsa')) {
    function associaRuoloRisorsa($idTipoRuolo, $idRisorsa)
    {
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_ruoli_risorse (idTipoRuolo, idRisorsa, dataCreazione) VALUES (:idTipoRuolo, :idRisorsa, current_timestamp )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->execute();
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Associato ruolo " . $idTipoRuolo . " alla risorsa " . $idRisorsa);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: dissociaRuoloRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('dissociaRuoloRisorsa')) {
    function dissociaRuoloRisorsa($idTipoRuolo, $idRisorsa)
    {
        verificaValiditaToken();

        $sql = "DELETE FROM " . PREFISSO_TAVOLA . "_ruoli_risorse WHERE idTipoRuolo = :idTipoRuolo AND  idRisorsa = :idRisorsa";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();
        chiudiConnessione($conn);

        if ($numeroRecordModificati != 1) {
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Dissociato ruolo " . $idTipoRuolo . " dalla risorsa " . $idRisorsa);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRisorsePerRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/



if (!function_exists('getRisorsePerRuolo')) {
    function getRisorsePerRuolo($idTipoRuolo, $pagina)
    {

        verificaValiditaToken();

        if (str_starts_with($idTipoRuolo, "AMM_")) {
        }


        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        if (str_starts_with($idTipoRuolo, "AMM")) {
            $sql = "SELECT u.nomeMetodo, u.descrizione, u.idRisorsa, r.dataCreazione, r.idTipoRuolo  FROM " . PREFISSO_TAVOLA . "_risorse u LEFT JOIN " . PREFISSO_TAVOLA . "_ruoli_risorse r ON u.idRisorsa = r.idRisorsa AND (idTipoRuolo=:idTipoRuolo OR idTipoRuolo IS NULL)  WHERE u.dataEliminazione IS NULL AND u.idRisorsa LIKE CONCAT('AMM', '%') ORDER BY u.idRisorsa LIMIT :pagina, " . ELEMENTI_PER_PAGINA;
        } else if (str_starts_with($idTipoRuolo, "USER")) {
            $sql = "SELECT u.nomeMetodo, u.descrizione, u.idRisorsa, r.dataCreazione, r.idTipoRuolo  FROM " . PREFISSO_TAVOLA . "_risorse u LEFT JOIN " . PREFISSO_TAVOLA . "_ruoli_risorse r ON u.idRisorsa = r.idRisorsa AND (idTipoRuolo=:idTipoRuolo OR idTipoRuolo IS NULL)  WHERE u.dataEliminazione IS NULL AND u.idRisorsa LIKE CONCAT('USER', '%') ORDER BY u.idRisorsa LIMIT :pagina, " . ELEMENTI_PER_PAGINA;
        } else {
            $sql = "SELECT u.nomeMetodo, u.descrizione, u.idRisorsa, r.dataCreazione, r.idTipoRuolo  FROM " . PREFISSO_TAVOLA . "_risorse u LEFT JOIN " . PREFISSO_TAVOLA . "_ruoli_risorse r ON u.idRisorsa = r.idRisorsa AND (idTipoRuolo=:idTipoRuolo OR idTipoRuolo IS NULL)  WHERE u.dataEliminazione IS NULL ORDER BY u.idRisorsa LIMIT :pagina, " . ELEMENTI_PER_PAGINA;
        }


        generaLogSuFile($sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: associaRuoloVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('associaRuoloVoceMenu')) {
    function associaRuoloVoceMenu($idTipoRuolo, $idVoceMenu)
    {
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_ruoli_voci_menu (idTipoRuolo, idVoceMenu, dataCreazione) VALUES (:idTipoRuolo, :idVoceMenu, current_timestamp )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();
        chiudiConnessione($conn);

        generaLogSuBaseDati("DEBUG", "Associato ruolo " . $idTipoRuolo . " alla voce di menu " . $idVoceMenu);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: dissociaRuoloVoceMenu
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('dissociaRuoloVoceMenu')) {
    function dissociaRuoloVoceMenu($idTipoRuolo, $idVoceMenu)
    {
        verificaValiditaToken();

        $sql = "DELETE FROM " . PREFISSO_TAVOLA . "_ruoli_voci_menu WHERE idTipoRuolo = :idTipoRuolo AND  idVoceMenu = :idVoceMenu";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':idVoceMenu', $idVoceMenu);
        $stmt->execute();
        

        $numeroRecordModificati = $stmt->rowCount();
        chiudiConnessione($conn);

        if ($numeroRecordModificati != 1) {
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Dissociato ruolo " . $idTipoRuolo . " dalla risorsa " . $idVoceMenu);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getVociMenuPerRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/



if (!function_exists('getVociMenuPerRuolo')) {
    function getVociMenuPerRuolo($idTipoRuolo, $pagina)
    {

        verificaValiditaToken();


        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT u.idVoceMenu, u.descrizione, u.path, u.icona, r.dataCreazione, r.idTipoRuolo  FROM " . PREFISSO_TAVOLA . "_voci_menu u LEFT JOIN " . PREFISSO_TAVOLA . "_ruoli_voci_menu r ON u.idVoceMenu = r.idVoceMenu AND (idTipoRuolo=:idTipoRuolo OR idTipoRuolo IS NULL) WHERE u.dataEliminazione IS NULL ORDER BY r.dataCreazione DESC, u.descrizione ASC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        generaLogSuFile($sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result;
    }
}
