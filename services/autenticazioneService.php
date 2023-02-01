<?php

if (!function_exists('getMedotoAutenticazionePredefinito')) {
    function getMedotoAutenticazionePredefinito($email)
    {

        $emailCifrata = cifraStringa($email);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT t.idTipoMetodoLogin as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_login m, " . PREFISSO_TAVOLA . "_t_metodi_login t WHERE u.email = :email AND u.idUtente = m.idUtente AND m.idTipoMetodoLogin = t.idTipoMetodoLogin AND m.isPredefinito = true AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante la ricerca del metodo di autenticazione predefinito");

        if (count($result) == 0) {
            $tmp = "EMAIL_PSW_SIX_EMAIL";
            $stmt = $conn->prepare("SELECT idTipoMetodoLogin as codice, descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
            $stmt->bindParam(':idTipoMetodoLogin', $tmp);
            $stmt->execute();
            $result = $stmt->fetchAll();


            $oggetto = new stdClass();
            $oggetto->codiceMetodoPredefinito = $result[0]["codice"];
            $oggetto->descrizioneMetodoPredefinito = substr($result[0]["descrizione"], 0, strpos($result[0]["descrizione"], "#") - 1);
            return $oggetto;
        } else {
            $oggetto = new stdClass();
            $oggetto->codiceMetodoPredefinito = $result[0]["codice"];
            $oggetto->descrizioneMetodoPredefinito = substr($result[0]["descrizione"], 0, strpos($result[0]["descrizione"], "#") - 1);
            return $oggetto;
        }
    }
}

if (!function_exists('getUtente')) {
    function getUtente($emailCifrata, $passwordCifrata)
    {
        generaLogSuFile($emailCifrata);
        generaLogSuFile($passwordCifrata);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente, nome, cognome FROM " . PREFISSO_TAVOLA . "_utenti WHERE email = :email AND password = :password AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->bindParam(':password', $passwordCifrata);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0)
            throw new AccessoNonAutorizzatoLoginException();

        return $result;
    }
}

if (!function_exists('effettuaAutenticazione')) {
    function effettuaAutenticazione($email, $password, $tipoAutenticazione)
    {

        $emailCifrata = cifraStringa($email);
        $passwordCifrata = md5(md5($password));

        if ($tipoAutenticazione == null) {

            $utente = getUtente($emailCifrata, $passwordCifrata);
            $idUtente = $utente[0]["idUtente"];
            $nome = decifraStringa($utente[0]["nome"]);
            $cognome = decifraStringa($utente[0]["cognome"]);
            $idTipoLogin = getMedotoSecondoFattorePreferito($idUtente);
            $idLogin = inserisciLogin($idUtente, $idTipoLogin);
            $codice = inserisciCodiceSecondoFattore($idLogin);

            if ($idTipoLogin == "EMAIL_PSW_SIX_EMAIL") {
                inviaCodiceSecondoFattoreViaEmail($email, $codice, $nome, $cognome);
            }

            $descrizione = getIstruzioniSecondoFattore($idTipoLogin);

            $oggetto = new stdClass();
            $oggetto->idLogin = $idLogin;
            $oggetto->descrizione = substr($descrizione, strpos($descrizione, "#") + 2);
            return $oggetto;
        } else {

            verificaEsistenzaMetodoSecondoFattorePerUtente($emailCifrata, $tipoAutenticazione);

            $utente = getUtente($emailCifrata, $passwordCifrata);
            $idUtente = $utente[0]["idUtente"];
            $nome = decifraStringa($utente[0]["nome"]);
            $cognome = decifraStringa($utente[0]["cognome"]);
            $idLogin = inserisciLogin($idUtente, $tipoAutenticazione);
            $codice = inserisciCodiceSecondoFattore($idLogin);

            if ($tipoAutenticazione == "EMAIL_PSW_SIX_EMAIL") {
                inviaCodiceSecondoFattoreViaEmail($email, $codice, $nome, $cognome);
            }

            $descrizione = getIstruzioniSecondoFattore($tipoAutenticazione);

            $oggetto = new stdClass();
            $oggetto->idLogin = $idLogin;
            $oggetto->descrizione = substr($descrizione, strpos($descrizione, "#") + 2);
            return $oggetto;

        }
    }
}

if (!function_exists('getIstruzioniSecondoFattore')) {
    function getIstruzioniSecondoFattore($idTipoMetodoLogin)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
        $stmt->bindParam(':idTipoMetodoLogin', $idTipoMetodoLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]["descrizione"];
    }
}

if (!function_exists('inviaCodiceSecondoFattoreViaEmail')) {
    function inviaCodiceSecondoFattoreViaEmail($email, $codice, $nome, $cognome)
    {
        $subject = NOME_APPLICAZIONE . " - Autenticazione a due fattori";
        $messaggio = "Ciao " . $cognome . " " . $nome . ", \n Inserisci il codice di verifica " . $codice . " per completare l'autenticazione";
        $headers = "From: noreply@riccardoriggi.it";
        mail($email, $subject, $messaggio, $headers);
        generaLogSuFile("Invio un'email a " . $email . " con il seguente testo: " . $messaggio);
    }
}

if (!function_exists('getMedotoSecondoFattorePreferito')) {
    function getMedotoSecondoFattorePreferito($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTipoMetodoLogin FROM " . PREFISSO_TAVOLA . "_metodi_login WHERE idUtente = :idUtente AND dataFineValidita IS NULL AND isPredefinito = true");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di generazione del codice per il secondo fattore");

        return count($result) == 0 ? "EMAIL_PSW_SIX_EMAIL" : $result[0]["idTipoMetodoLogin"];
    }
}

if (!function_exists('inserisciLogin')) {
    function inserisciLogin($idUtente, $idTipoLogin)
    {
        $idLogin = generaUUID();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_login(idLogin, idUtente, idTipoLogin) VALUES (:idLogin, :idUtente, :idTipoLogin)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':idTipoLogin', $idTipoLogin);
        $stmt->execute();
        return $idLogin;
    }
}

if (!function_exists('inserisciCodiceSecondoFattore')) {
    function inserisciCodiceSecondoFattore($idLogin)
    {
        $idTwoFact = generaUUID();
        $codice = generaCodiceSeiCifre();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_two_fact(idTwoFact, idLogin, codice) VALUES (:idTwoFact, :idLogin, :codice)");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        return $codice;
    }
}

if (!function_exists('verificaEsistenzaMetodoSecondoFattorePerUtente')) {
    function verificaEsistenzaMetodoSecondoFattorePerUtente($emailCifrata, $idTipoMetodoLogin)
    {
        $conn = apriConnessione();
        $sql = "SELECT t.idTipoMetodoLogin as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_login m, " . PREFISSO_TAVOLA . "_t_metodi_login t WHERE u.email = :email AND m.idTipoMetodoLogin = :idTipoMetodoLogin AND u.idUtente = m.idUtente AND m.idTipoMetodoLogin = t.idTipoMetodoLogin AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL";
        generaLogSuFile($sql);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->bindParam(':idTipoMetodoLogin', $idTipoMetodoLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();
    }
}
