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
            throw new OtterGuardianException(500, "Errore di configurazione, hai impostato più di un metodo di autenticazione predefinito");

        if (count($result) == 0) {
            $tmp = "EMAIL_PSW_SIX_EMAIL";
            $stmt = $conn->prepare("SELECT idTipoMetodoLogin as codice, descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
            $stmt->bindParam(':idTipoMetodoLogin', $tmp);
            $stmt->execute();
            $result = $stmt->fetchAll();
            chiudiConnessione($conn);

            incrementaContatoreAlert();
            generaLogSuBaseDati("INFO", "Inserito un indirizzo email non presente nel sistema");


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
        chiudiConnessione($conn);

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0){
            incrementaContatoreAlert();
            generaLogSuBaseDati("INFO", "Inserita coppia email/password errata");
            throw new AccessoNonAutorizzatoLoginException();
        }
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
        chiudiConnessione($conn);

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0){
            incrementaContatoreAlert();
            generaLogSuBaseDati("INFO", "Cercato un utente non registrato senza password e con un tipo autenticazione tra quelli presenti in banca dati");
            throw new AccessoNonAutorizzatoLoginException();
        }
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getMetodiAutenticazioneSupportati
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('getMetodiAutenticazioneSupportati')) {
    function getMetodiAutenticazioneSupportati($email)
    {

        $emailCifrata = cifraStringa($email);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT t.idTipoMetodoLogin as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_login m, " . PREFISSO_TAVOLA . "_t_metodi_login t WHERE u.email = :email AND u.idUtente = m.idUtente AND m.idTipoMetodoLogin = t.idTipoMetodoLogin AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) == 0) {
            $tmp = "EMAIL_PSW_SIX_EMAIL";
            $stmt = $conn->prepare("SELECT idTipoMetodoLogin as codice, descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
            $stmt->bindParam(':idTipoMetodoLogin', $tmp);
            $stmt->execute();
            $result = $stmt->fetchAll();
            chiudiConnessione($conn);
            generaLogSuBaseDati("INFO", "Inserito un indirizzo email non presente nel sistema: ".$email);
            return $result;
        } else {
            chiudiConnessione($conn);
            return $result;
        }
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
                //TODO SCOMMENTARE PER ABILITARE
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
        chiudiConnessione($conn);
        return $result[0]["descrizione"];
    }
}

if (!function_exists('inviaCodiceSecondoFattoreViaEmail')) {
    function inviaCodiceSecondoFattoreViaEmail($email, $codice, $nome, $cognome)
    {
        $subject = NOME_APPLICAZIONE . " - Autenticazione a due fattori";
        $messaggio = "Ciao " . $cognome . " " . $nome . ", \n Inserisci il codice di verifica " . $codice . " per completare l'autenticazione";
        $headers = "From: noreply@riccardoriggi.it";
        if(ABILITA_INVIO_EMAIL){
            mail($email, $subject, $messaggio, $headers);
        }
        
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
        chiudiConnessione($conn);

        if (count($result) > 1)
            throw new OtterGuardianException(500, "Errore di configurazione, hai selezionato più metodi predefiniti");

        return count($result) == 0 ? "EMAIL_PSW_SIX_EMAIL" : $result[0]["idTipoMetodoLogin"];
    }
}

if (!function_exists('inserisciLogin')) {
    function inserisciLogin($idUtente, $idTipoLogin)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $idLogin = generaUUID();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_login(idLogin, idUtente, idTipoLogin, indirizzoIp, userAgent) VALUES (:idLogin, :idUtente, :idTipoLogin, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':idTipoLogin', $idTipoLogin);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        chiudiConnessione($conn);
        return $idLogin;
    }
}

if (!function_exists('inserisciCodiceSecondoFattore')) {
    function inserisciCodiceSecondoFattore($idLogin)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $idTwoFact = generaUUID();
        $codice = generaCodiceSeiCifre();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_two_fact(idTwoFact, idLogin, codice, indirizzoIp) VALUES (:idTwoFact, :idLogin, :codice, :indirizzoIp)");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':codice', $codice);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        chiudiConnessione($conn);
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
        chiudiConnessione($conn);

        if (count($result) != 1){
            generaLogSuBaseDati("INFO", "Tentativo di ricerca di un indirizzo email con metodo di autenticazione");
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: confermaAutenticazione
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('confermaAutenticazione')) {
    function confermaAutenticazione($idLogin, $codice)
    {
        $idUtente = getIdUtenteByIdLogin($idLogin);
        $idTipoLogin = getIdTipoLoginByIdLogin($idLogin);
        getUtenteDecifrato($idUtente);
        if ($idTipoLogin != "EMAIL_PSW_BACKUP_CODE") {
            confrontaConUltimoTentativoDiLogin($idLogin, $idUtente);
            $idTwoFact = verificaCodiceSecondoFattore($idLogin, $codice);
            aggiornoDataUtilizzoCodiceSecondoFattore($idTwoFact);
        } else {
            verificaCodiceBackup($idUtente, $codice);
            registraUtilizzoCodiceBackup($idUtente, $codice);
        }

        invalidoTokenPrecedenti($idUtente);
        $token = registraToken($idLogin, $idUtente, $_SERVER["HTTP_USER_AGENT"]);
        aggiornoLoginConToken($idLogin, $token);
        header('TOKEN: ' . $token);
    }
}

if (!function_exists('getUtenteDecifrato')) {
    function getUtenteDecifrato($idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente, nome, cognome, email, dataCreazione, dataUltimaModifica FROM " . PREFISSO_TAVOLA . "_utenti WHERE idUtente = :idUtente AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0){
            incrementaContatoreAlert();
            throw new OtterGuardianException(404, "Utente non trovato");
        }
        return $result;
    }
}

if (!function_exists('registraUtilizzoCodiceBackup')) {
    function registraUtilizzoCodiceBackup($idUtente, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_codici_backup SET dataUtilizzo = current_timestamp WHERE idUtente = :idUtente AND codice = :codice AND dataUtilizzo IS NULL and dataEliminazione IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('verificaCodiceBackup')) {
    function verificaCodiceBackup($idUtente, $codice)
    {
        $sql = "SELECT codice FROM " . PREFISSO_TAVOLA . "_codici_backup WHERE idUtente = :idUtente AND codice = :codice AND dataUtilizzo IS NULL AND dataEliminazione IS NULL";

        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        $resultUno = $stmt->fetchAll();
        

        if (count($resultUno) != 1) {
            incrementaContatoreAlert();
            $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_utenti SET tentativiCodiciBackup = tentativiCodiciBackup + 1 WHERE idUtente = :idUtente ");
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT tentativiCodiciBackup FROM " . PREFISSO_TAVOLA . "_utenti WHERE idUtente = :idUtente AND dataEliminazione IS NULL AND dataBlocco is NULL");
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->execute();
            $result = $stmt->fetchAll();

            if ($result[0]["tentativiCodiciBackup"] > 5) {
                $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_utenti SET dataBlocco = current_timestamp WHERE idUtente = :idUtente ");
                $stmt->bindParam(':idUtente', $idUtente);
                $stmt->execute();
            }

            throw new AccessoNonAutorizzatoLoginException();
        }
    }
}



if (!function_exists('getIdTipoLoginByIdLogin')) {
    function getIdTipoLoginByIdLogin($idLogin)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTipoLogin FROM " . PREFISSO_TAVOLA . "_login WHERE idLogin = :idLogin AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1){
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Non sono stati trovati tentativi di accesso ancora in corso di validità, probabilmente hai superato il tempo limite, effettua nuovamente la procedura di autenticazione");
        }
        return $result[0]["idTipoLogin"];
    }
}

if (!function_exists('getIdUtenteByIdLogin')) {
    function getIdUtenteByIdLogin($idLogin)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_login WHERE idLogin = :idLogin AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1){
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Non sono stati trovati tentativi di accesso ancora in corso di validità, probabilmente hai superato il tempo limite, effettua nuovamente la procedura di autenticazione");
        }

        return $result[0]["idUtente"];
    }
}

if (!function_exists('confrontaConUltimoTentativoDiLogin')) {
    function confrontaConUltimoTentativoDiLogin($idLogin, $idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idLogin FROM " . PREFISSO_TAVOLA . "_login WHERE idUtente = :idUtente AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4 ORDER BY dataCreazione desc LIMIT 1");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1){
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        if ($result[0]["idLogin"] != $idLogin){
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Verifica di stare autorizzando il tentativo di accesso più recente");
        }
    }
}

if (!function_exists('verificaCodiceSecondoFattore')) {
    function verificaCodiceSecondoFattore($idLogin, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTwoFact, tentativi, codice FROM " . PREFISSO_TAVOLA . "_two_fact WHERE idLogin = :idLogin AND dataUtilizzo IS NULL AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4 ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
        $result = $stmt->fetchAll();
        

        if (count($result) != 1){
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_two_fact SET tentativi = tentativi + 1 WHERE idLogin = :idLogin ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();

        if ($result[0]["tentativi"] > 5){
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Hai superato il numero massimo di tentativi");
        }

        if ($result[0]["codice"] != $codice){
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

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
        chiudiConnessione($conn);
    }
}

if (!function_exists('invalidoTokenPrecedenti')) {
    function invalidoTokenPrecedenti($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente AND dataFineValidita IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('registraToken')) {
    function registraToken($idLogin, $idUtente, $userAgent)
    {
        $token = generaUUID();
        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_token(token, idLogin, idUtente, dataGenerazione, dataInizioValidita, indirizzoIp, userAgent) VALUES (:token, :idLogin, :idUtente, current_timestamp, current_timestamp, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $userAgent);
        $stmt->execute();
        chiudiConnessione($conn);

        return $token;
    }
}

if (!function_exists('registraTokenQrCode')) {
    function registraTokenQrCode($idLogin, $idUtente, $userAgent, $indirizzoIp)
    {
        $token = generaUUID();

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_token(token, idLogin, idUtente, dataGenerazione, dataInizioValidita, indirizzoIp, userAgent) VALUES (:token, :idLogin, :idUtente, current_timestamp, current_timestamp, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $userAgent);
        $stmt->execute();
        chiudiConnessione($conn);

        return $token;
    }
}

if (!function_exists('aggiornoLoginConToken')) {
    function aggiornoLoginConToken($idLogin, $token)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_login SET token = :token WHERE idLogin = :idLogin ");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: recuperaTokenDaLogin
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('recuperaTokenDaLogin')) {
    function recuperaTokenDaLogin($idLogin)
    {
        $token = recuperaTokenDaIdLogin($idLogin);
        header('TOKEN: ' . $token);
    }
}

if (!function_exists('recuperaTokenDaIdLogin')) {
    function recuperaTokenDaIdLogin($idLogin)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT token FROM " . PREFISSO_TAVOLA . "_token WHERE idLogin = :idLogin AND dataFineValidita IS NULL and indirizzoIp = :indirizzoIp AND userAgent = :userAgent ");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1){
            throw new AccessoNonAutorizzatoLoginException();
        }

        return  $result[0]["token"];
    }
}

if (!function_exists('recuperaTokenDaIdQrCode')) {
    function recuperaTokenDaIdQrCode($idLogin)
    {

        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT token FROM " . PREFISSO_TAVOLA . "_token WHERE idLogin = :idLogin AND dataFineValidita IS NULL and indirizzoIp = :indirizzoIp AND userAgent = :userAgent");
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1){
            throw new AccessoNonAutorizzatoLoginException();
        }

        return  $result[0]["token"];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: generaQrCode
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('generaQrCode')) {
    function generaQrCode()
    {
        $idQrCode = generaUUID();
        $indirizzoIp = cifraStringa(getIndirizzoIp());

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_qr_code (idQrCode, dataInizioValidita, indirizzoIp, userAgent) VALUES (:idQrCode, current_timestamp, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':idQrCode', $idQrCode);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        chiudiConnessione($conn);

        return $idQrCode;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: recuperaTokenDaQrCode
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('recuperaTokenDaQrCode')) {
    function recuperaTokenDaQrCode($idLogin)
    {
        $token = recuperaTokenDaIdQrCode($idLogin);
        header('TOKEN: ' . $token);
    }
}
