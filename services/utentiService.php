<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getListaUtenti
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getListaUtenti')) {
    function getListaUtenti($pagina)
    {
        verificaValiditaToken();
        $paginaDaEstrarre = ($pagina - 1) * ELEMENTI_PER_PAGINA;

        $sql = "SELECT idUtente, nome, cognome, email, dataBlocco FROM " . PREFISSO_TAVOLA . "_utenti WHERE dataEliminazione IS NULL ORDER BY idUtente LIMIT :pagina, " . ELEMENTI_PER_PAGINA;

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pagina', $paginaDaEstrarre, PDO::PARAM_INT);
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


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: inserisciUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('inserisciUtente')) {
    function inserisciUtente($nomeInChiaro, $cognomeInChiaro, $emailInChiaro, $passwordInChiaro)
    {
        verificaValiditaToken();

        $nome = cifraStringa($nomeInChiaro);
        $cognome = cifraStringa($cognomeInChiaro);
        $email = cifraStringa($emailInChiaro);
        $password = md5(md5($passwordInChiaro));

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_utenti (nome, cognome, email, password, dataCreazione) VALUES (:nome, :cognome, :email, :password, current_timestamp )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cognome', $cognome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $idUtenteInserito = $conn->lastInsertId();
        associaRuoloUtenteDefault($idUtenteInserito);
        abilitaTipoRecuperoPasswordDefault($idUtenteInserito);
        abilitaTipoMetodoLoginDefault($idUtenteInserito);
    }
}

if (!function_exists('abilitaTipoRecuperoPasswordDefault')) {
    function abilitaTipoRecuperoPasswordDefault($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_metodi_rec_psw (idUtente, idTipoMetodoRecPsw, dataInizioValidita) VALUES (:idUtente, 'REC_PSW_EMAIL_SIX_EMAIL', current_timestamp)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

if (!function_exists('associaRuoloUtenteDefault')) {
    function associaRuoloUtenteDefault($idUtente)
    {

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_ruoli_utenti (idTipoRuolo, idUtente, dataCreazione) VALUES ('USER', :idUtente, current_timestamp )";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

if (!function_exists('abilitaTipoMetodoLoginDefault')) {
    function abilitaTipoMetodoLoginDefault($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_metodi_login (idUtente, idTipoMetodoLogin, dataInizioValidita) VALUES (:idUtente, 'EMAIL_PSW_SIX_EMAIL', current_timestamp)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getUtente')) {
    function getUtente($idUtente)
    {
        verificaValiditaToken();

        $sql = "SELECT idUtente, nome, cognome, email, dataBlocco FROM " . PREFISSO_TAVOLA . "_utenti WHERE dataEliminazione IS NULL AND idUtente = :idUtente";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
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
        return $array[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: modificaUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('modificaUtente')) {
    function modificaUtente($nomeInChiaro, $cognomeInChiaro, $idUtente)
    {
        verificaValiditaToken();

        $nome = cifraStringa($nomeInChiaro);
        $cognome = cifraStringa($cognomeInChiaro);

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_utenti SET nome= :nome, cognome= :cognome WHERE idUtente = :idUtente AND dataEliminazione IS NULL ";


        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cognome', $cognome);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: eliminaUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('eliminaUtente')) {
    function eliminaUtente($idUtente)
    {
        
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_utenti SET dataEliminazione = current_timestamp WHERE idUtente = :idUtente AND dataEliminazione IS NULL ";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();

    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: bloccaUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('bloccaUtente')) {
    function bloccaUtente($idUtente)
    {
        
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_utenti SET dataBlocco = current_timestamp WHERE idUtente = :idUtente AND dataEliminazione IS NULL and dataBlocco IS NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();

    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: sbloccaUtente
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('sbloccaUtente')) {
    function sbloccaUtente($idUtente)
    {
        
        verificaValiditaToken();

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_utenti SET dataBlocco = NULL WHERE idUtente = :idUtente AND dataEliminazione IS NULL and dataBlocco IS NOT NULL";

        $conn = apriConnessione();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();

    }
}
