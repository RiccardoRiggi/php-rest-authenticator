<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRisorse
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getRisorse')) {
    function getRisorse($pagina)
    {
        //verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT idRisorsa, nomeMetodo, descrizione FROM " . PREFISSO_TAVOLA . "_risorse WHERE dataEliminazione IS NULL ORDER BY idRisorsa LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inserisciRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inserisciRisorsa')) {
    function inserisciRisorsa($idRisorsa, $nomeMetodo, $descrizione)
    {
        //verificaValiditaToken();

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_risorse (idRisorsa, nomeMetodo, descrizione, dataCreazione) VALUES (:idRisorsa, :nomeMetodo , :descrizione ,current_timestamp)";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->bindParam(':nomeMetodo', $nomeMetodo);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->execute();

        generaLogSuBaseDati("DEBUG", "Inserimento nuova risorsa con identificativo " . $idRisorsa);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: modificaRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('modificaRisorsa')) {
    function modificaRisorsa($nomeMetodo, $descrizione, $idRisorsa)
    {
        //verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_risorse SET nomeMetodo= :nomeMetodo ,descrizione= :descrizione WHERE idRisorsa = :idRisorsa AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nomeMetodo', $nomeMetodo);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->execute();

        generaLogSuBaseDati("DEBUG", "Modifica della risorsa con identificativo " . $idRisorsa);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('eliminaRisorsa')) {
    function eliminaRisorsa($idRisorsa)
    {
        //verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_risorse SET dataEliminazione = current_timestamp WHERE idRisorsa = :idRisorsa AND dataEliminazione IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->execute();

        $numeroRecordModificati = $stmt->rowCount();

        if ($numeroRecordModificati != 1) {
            generaLogSuBaseDati("ERROR", "Tentativo di eliminazione di una voce di menu non esistente. Identificativo inserito: " . $idRisorsa);
            throw new OtterGuardianException(500, "Non esiste un record con l'identificativo indicato");
        }

        generaLogSuBaseDati("DEBUG", "Eliminazione della voce di menu con identificativo " . $idRisorsa);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRisorsa
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getRisorsa')) {
    function getRisorsa($idRisorsa)
    {
        //verificaValiditaToken();

        $sql = "SELECT idRisorsa, nomeMetodo, descrizione FROM " . PREFISSO_TAVOLA . "_risorse WHERE dataEliminazione IS NULL AND idRisorsa = :idRisorsa ";

        generaLogSuBaseDati("DEBUG", $sql);

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idRisorsa', $idRisorsa);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }
}
