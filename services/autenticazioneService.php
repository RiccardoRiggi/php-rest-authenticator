<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getMetodoAutenticazionePredefinito
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


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

if (!function_exists('getUtenteSenzaPassword')) {
    function getUtenteSenzaPassword($emailCifrata)
    {
        generaLogSuFile($emailCifrata);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente, nome, cognome FROM " . PREFISSO_TAVOLA . "_utenti WHERE email = :email AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0)
            throw new AccessoNonAutorizzatoLoginException();

        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: effettuaAutenticazione
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


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

            if (str_contains($tipoAutenticazione, "PSW")) {
                $utente = getUtente($emailCifrata, $passwordCifrata);
            } else {
                $utente = getUtenteSenzaPassword($emailCifrata);
            }


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

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: confermaAutenticazione
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('confermaAutenticazione')) {
    function confermaAutenticazione($idLogin, $codice)
    {
        $idUtente = getIdUtenteByIdLogin($idLogin);
        confrontaConUltimoTentativoDiLogin($idLogin, $idUtente);
        $idTwoFact = verificaCodiceSecondoFattore($idLogin, $codice);
        aggiornoDataUtilizzoCodiceSecondoFattore($idTwoFact);
        invalidoSessioniPrecedenti($idUtente);
        $idSessione = registraSessione($idLogin, $idUtente);
        aggiornoLoginConSessione($idLogin,$idSessione);

        
        $impronta = null;
        if (IMPRONTE_SESSIONE_ABILITATE) {
            $impronta = registraImprontaSessione($idSessione);
        }

        header('SESSIONE: ' . $idSessione);
        if (IMPRONTE_SESSIONE_ABILITATE) {
            header('IMPRONTA: ' . $impronta);

        }

    }
}

if (!function_exists('getIdUtenteByIdLogin')) {
    function getIdUtenteByIdLogin($idLogin)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_login WHERE idLogin = :idLogin ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return $result[0]["idUtente"];
    }
}

if (!function_exists('confrontaConUltimoTentativoDiLogin')) {
    function confrontaConUltimoTentativoDiLogin($idLogin, $idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idLogin FROM " . PREFISSO_TAVOLA . "_login WHERE idUtente = :idUtente ORDER BY dataCreazione desc LIMIT 1");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        if ($result[0]["idLogin"] != $idLogin)
            throw new AccessoNonAutorizzatoLoginException();
    }
}

if (!function_exists('verificaCodiceSecondoFattore')) {
    function verificaCodiceSecondoFattore($idLogin, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTwoFact, tentativi FROM " . PREFISSO_TAVOLA . "_two_fact WHERE idLogin = :idLogin AND codice = :codice AND dataUtilizzo IS NULL AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4 ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_two_fact SET tentativi = tentativi + 1 WHERE idLogin = :idLogin ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();

        if ($result[0]["tentativi"]>5)
            throw new AccessoNonAutorizzatoLoginException();

        return $result[0]["idTwoFact"];
    }
}

if (!function_exists('aggiornoDataUtilizzoCodiceSecondoFattore')) {
    function aggiornoDataUtilizzoCodiceSecondoFattore($idTwoFact)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_two_fact SET dataUtilizzo = current_timestamp WHERE idTwoFact = :idTwoFact ");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->execute();
    }
}

if (!function_exists('invalidoSessioniPrecedenti')) {
    function invalidoSessioniPrecedenti($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_sessioni SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

if (!function_exists('registraSessione')) {
    function registraSessione($idLogin, $idUtente)
    {
        $idSessione = generaUUID();
        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_sessioni(idSessione, idLogin, idUtente, dataGenerazione, dataInizioValidita, indirizzoIp, userAgent) VALUES (:idSessione, :idLogin, :idUtente, current_timestamp, current_timestamp, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();

        return $idSessione;
    }
}

if (!function_exists('aggiornoLoginConSessione')) {
    function aggiornoLoginConSessione($idLogin,$idSessione)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_login SET idSessione = :idSessione WHERE idLogin = :idLogin ");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
    }
}

if (!function_exists('registraImprontaSessione')) {
    function registraImprontaSessione($idSessione)
    {
        $idImpronta = generaUUID();

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_sessioni_impronte(idSessione, idImpronta, dataGenerazione) VALUES (:idSessione, :idImpronta, current_timestamp)");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->bindParam(':idImpronta', $idImpronta);
        $stmt->execute();

        return $idImpronta;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: recuperaSessioneDaLogin
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('recuperaSessioneDaLogin')) {
    function recuperaSessioneDaLogin($idLogin)
    {

        $idSessione = recuperaSessioneDaIdLogin($idLogin);

        $impronta = null;
        if (IMPRONTE_SESSIONE_ABILITATE) {
            $impronta = recuperaPrimaImprontaDaSessione($idSessione);
        }

        header('SESSIONE: ' . $idSessione);
        if (IMPRONTE_SESSIONE_ABILITATE) {
            header('IMPRONTA: ' . $impronta);

        }

    }
}

if (!function_exists('recuperaSessioneDaIdLogin')) {
    function recuperaSessioneDaIdLogin($idLogin)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idSessione FROM " . PREFISSO_TAVOLA . "_sessioni WHERE idLogin = :idLogin AND dataFineValidita IS NULL and indirizzoIp = :indirizzoIp AND userAgent = :userAgent ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return  $result[0]["idSessione"];   

    
    }
}

if (!function_exists('recuperaPrimaImprontaDaSessione')) {
    function recuperaPrimaImprontaDaSessione($idSessione)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idImpronta FROM " . PREFISSO_TAVOLA . "_sessioni_impronte WHERE idSessione = :idSessione ORDER BY dataGenerazione ASC LIMIT 1");
        $stmt->bindParam(':idSessione', $idSessione);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return  $result[0]["idImpronta"];   

    
    }
}