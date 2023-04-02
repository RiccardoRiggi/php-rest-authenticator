<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getListaNotifiche
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getListaNotifiche')) {
    function getListaNotifiche($pagina)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;


        $sql = "SELECT idNotifica, titolo, testo, dataCreazione FROM " . PREFISSO_TAVOLA . "_t_notifiche WHERE dataEliminazione IS NULL ORDER BY dataCreazione DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inserisciNotifica
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inserisciNotifica')) {
    function inserisciNotifica($titolo, $testo)
    {
        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_t_notifiche (titolo, testo) VALUES (:titolo, :testo )";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titolo', $titolo);
        $stmt->bindParam(':testo', $testo);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: modificaNotifica
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('modificaNotifica')) {
    function modificaNotifica($titolo, $testo, $idNotifica)
    {
        verificaValiditaToken();

        $sql = "SELECT idNotifica FROM " . PREFISSO_TAVOLA . "_notifiche WHERE idNotifica = :idNotifica AND dataEliminazione IS NULL";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result != null) {
            throw new OtterGuardianException(500, "Non puoi modificare una notifica che è già stata inviata");
        }

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_t_notifiche SET titolo= :titolo ,testo= :testo WHERE idNotifica = :idNotifica AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titolo', $titolo);
        $stmt->bindParam(':testo', $testo);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaNotifica
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaNotifica')) {
    function eliminaNotifica($idNotifica)
    {
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_t_notifiche SET dataEliminazione = current_timestamp WHERE idNotifica = :idNotifica AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una notifica non esistente. Identificativo inserito: " . $idNotifica);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNotifica
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNotifica')) {
    function getNotifica($idNotifica)
    {
        verificaValiditaToken();

        $sql = "SELECT idNotifica, titolo, testo, dataCreazione FROM " . PREFISSO_TAVOLA . "_t_notifiche WHERE dataEliminazione IS NULL AND idNotifica = :idNotifica ";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inviaNotificaUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inviaNotificaUtente')) {
    function inviaNotificaUtente($idNotifica, $idUtente)
    {

        verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_notifiche (idNotifica, idUtente) VALUES (:idNotifica, :idUtente )";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inviaNotificaRuolo
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inviaNotificaRuolo')) {
    function inviaNotificaRuolo($idNotifica, $idTipoRuolo)
    {

        verificaValiditaToken();
        $sql = "SELECT u.idUtente  FROM " . PREFISSO_TAVOLA . "_utenti u JOIN " . PREFISSO_TAVOLA . "_ruoli_utenti r ON u.idUtente = r.idUtente AND (idTipoRuolo=:idTipoRuolo) WHERE u.dataEliminazione IS NULL AND u.dataBlocco IS NULL ";

        generaLogSuFile($sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTipoRuolo', $idTipoRuolo);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach ($result as $value) {
            try {
                inviaNotificaUtente($idNotifica, $value["idUtente"]);
            } catch (Exception $e) {
                generaLogSuFile("Errore durante l'invio delle notifiche: " . $e->getMessage());
            }
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inviaNotificaTutti
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inviaNotificaTutti')) {
    function inviaNotificaTutti($idNotifica)
    {

        verificaValiditaToken();
        $sql = "SELECT idUtente FROM " . PREFISSO_TAVOLA . "_utenti WHERE dataEliminazione IS NULL ";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach ($result as $value) {
            try {
                inviaNotificaUtente($idNotifica, $value["idUtente"]);
            } catch (Exception $e) {
                generaLogSuFile("Errore durante l'invio delle notifiche: " . $e->getMessage());
            }
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNotificaLatoUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNotificaLatoUtente')) {
    function getNotificaLatoUtente($idNotifica)
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $sql = "SELECT t.idNotifica, titolo, testo, dataCreazione FROM " . PREFISSO_TAVOLA . "_t_notifiche t JOIN " . PREFISSO_TAVOLA . "_notifiche n ON t.idNotifica = n.idNotifica WHERE t.dataEliminazione IS NULL AND n.dataEliminazione IS NULL AND n.idNotifica = :idNotifica AND n.idUtente = :idUtente";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getNotificheLatoUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getNotificheLatoUtente')) {
    function getNotificheLatoUtente($pagina)
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;


        $sql = "SELECT n.idNotifica, titolo, testo, dataCreazione, dataLettura, dataInvio FROM " . PREFISSO_TAVOLA . "_t_notifiche t JOIN " . PREFISSO_TAVOLA . "_notifiche n ON t.idNotifica = n.idNotifica WHERE t.dataEliminazione IS NULL AND n.dataEliminazione IS NULL AND n.idUtente = :idUtente ORDER BY dataInvio DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaNotificaLatoUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaNotificaLatoUtente')) {
    function eliminaNotificaLatoUtente($idNotifica)
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_notifiche SET dataEliminazione = current_timestamp WHERE idNotifica = :idNotifica AND idUtente = :idUtente AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->bindParam(':idUtente', $idUtente);

        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una notifica non esistente. Identificativo inserito: " . $idNotifica);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: leggiNotificheLatoUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('leggiNotificheLatoUtente')) {
    function leggiNotificheLatoUtente()
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_notifiche SET dataLettura = current_timestamp WHERE idUtente = :idUtente AND dataEliminazione IS NULL";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getDestinatariNotifica
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getDestinatariNotifica')) {
    function getDestinatariNotifica($pagina, $idNotifica)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT u.idUtente, nome, cognome, email,idNotifica, dataInvio, dataLettura FROM " . PREFISSO_TAVOLA . "_utenti u LEFT JOIN " . PREFISSO_TAVOLA . "_notifiche n ON ( u.idUtente = n.idUtente OR n.idNotifica IS NULL ) WHERE u.dataEliminazione IS NULL AND n.dataEliminazione IS NULL AND (idNotifica = :idNotifica OR idNotifica IS NULL ) ORDER BY dataInvio DESC LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        generaLogSuFile($sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->bindParam(':idNotifica', $idNotifica);
        $stmt->execute();
        $result = $stmt->fetchAll();

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
