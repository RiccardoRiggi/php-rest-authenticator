<?php


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

        if (count($result) == 0) {
            incrementaContatoreAlert();
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

        if (count($result) > 1)
            throw new ErroreServerException("Errore durante il processo di autenticazione");

        if (count($result) == 0) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getMetodiRecuperoPasswordSupportati
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('getMetodiRecuperoPasswordSupportati')) {
    function getMetodiRecuperoPasswordSupportati($email)
    {

        $emailCifrata = cifraStringa($email);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT t.idTipoMetodoRecPsw as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_rec_psw m, " . PREFISSO_TAVOLA . "_t_metodi_rec_psw t WHERE u.email = :email AND u.idUtente = m.idUtente AND m.idTipoMetodoRecPsw = t.idTipoMetodoRecPsw AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL");
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) == 0) {
            $tmp = "REC_PSW_EMAIL_SIX_EMAIL";
            $stmt = $conn->prepare("SELECT idTipoMetodoRecPsw as codice, descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_rec_psw WHERE idTipoMetodoRecPsw = :idTipoMetodoRecPsw ");
            $stmt->bindParam(':idTipoMetodoRecPsw', $tmp);
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result;
        } else {
            return $result;
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: effettuaRichiestaRecuperoPassword
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('effettuaRichiestaRecuperoPassword')) {
    function effettuaRichiestaRecuperoPassword($email, $tipoRecuperoPassword)
    {

        $emailCifrata = cifraStringa($email);
        verificaEsistenzaMetodoRecuperoPasswordPerUtente($emailCifrata, $tipoRecuperoPassword);
        $utente = getUtenteSenzaPassword($emailCifrata);
        $idUtente = $utente[0]["idUtente"];
        $nome = decifraStringa($utente[0]["nome"]);
        $cognome = decifraStringa($utente[0]["cognome"]);
        $idRecPsw = inserisciRecPsw($idUtente, $tipoRecuperoPassword);
        $codice = inserisciCodiceSecondoFattorePerRecuperoPassword($idRecPsw);

        if ($tipoRecuperoPassword == "REC_PSW_EMAIL_SIX_EMAIL") {
            inviaCodiceSecondoFattoreRecuperoPassowrdViaEmail($email, $codice, $nome, $cognome);
        }

        $descrizione = getIstruzioniSecondoFattoreRecuperoPassword($tipoRecuperoPassword);

        $oggetto = new stdClass();
        $oggetto->idRecPsw = $idRecPsw;
        $oggetto->descrizione = substr($descrizione, strpos($descrizione, "#") + 2);
        return $oggetto;
    }
}

if (!function_exists('getIstruzioniSecondoFattoreRecuperoPassword')) {
    function getIstruzioniSecondoFattoreRecuperoPassword($idTipoMetodoRecPsw)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_rec_psw WHERE idTipoMetodoRecPsw = :idTipoMetodoRecPsw ");
        $stmt->bindParam(':idTipoMetodoRecPsw', $idTipoMetodoRecPsw);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]["descrizione"];
    }
}

if (!function_exists('inviaCodiceSecondoFattoreRecuperoPassowrdViaEmail')) {
    function inviaCodiceSecondoFattoreRecuperoPassowrdViaEmail($email, $codice, $nome, $cognome)
    {
        $subject = NOME_APPLICAZIONE . " - Autenticazione a due fattori";
        $messaggio = "Ciao " . $cognome . " " . $nome . ", \n Inserisci il codice di verifica " . $codice . " per completare la procedura di recupero password";
        $headers = "From: noreply@riccardoriggi.it";
        //TODO SCOMMENTARE PER ABILITARE
        //mail($email, $subject, $messaggio, $headers);
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
            throw new OtterGuardianException(500, "Errore di configurazione, hai selezionato più metodi predefiniti");

        return count($result) == 0 ? "EMAIL_PSW_SIX_EMAIL" : $result[0]["idTipoMetodoLogin"];
    }
}

if (!function_exists('inserisciRecPsw')) {
    function inserisciRecPsw($idUtente, $idTipoRecPsw)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $idRecPsw = generaUUID();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_rec_psw(idRecPsw, idUtente, idTipoRecPsw, indirizzoIp, userAgent) VALUES (:idRecPsw, :idUtente, :idTipoRecPsw, :indirizzoIp, :userAgent)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':idRecPsw', $idRecPsw);
        $stmt->bindParam(':idTipoRecPsw', $idTipoRecPsw);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->bindParam(':userAgent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->execute();
        return $idRecPsw;
    }
}

if (!function_exists('inserisciCodiceSecondoFattorePerRecuperoPassword')) {
    function inserisciCodiceSecondoFattorePerRecuperoPassword($idRecPsw)
    {
        $indirizzoIp = cifraStringa(getIndirizzoIp());
        $idTwoFact = generaUUID();
        $codice = generaCodiceSeiCifre();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_two_fact(idTwoFact, idRecPsw, codice, indirizzoIp) VALUES (:idTwoFact, :idRecPsw, :codice, :indirizzoIp)");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->bindParam(':idRecPsw', $idRecPsw);
        $stmt->bindParam(':codice', $codice);
        $stmt->bindParam(':indirizzoIp', $indirizzoIp);
        $stmt->execute();
        return $codice;
    }
}

if (!function_exists('verificaEsistenzaMetodoRecuperoPasswordPerUtente')) {
    function verificaEsistenzaMetodoRecuperoPasswordPerUtente($emailCifrata, $idTipoMetodoRecPsw)
    {
        $conn = apriConnessione();
        $sql = "SELECT t.idTipoMetodoRecPsw as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_rec_psw m, " . PREFISSO_TAVOLA . "_t_metodi_rec_psw t WHERE u.email = :email AND m.idTipoMetodoRecPsw = :idTipoMetodoRecPsw AND u.idUtente = m.idUtente AND m.idTipoMetodoRecPsw = t.idTipoMetodoRecPsw AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL";
        generaLogSuFile($sql);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $emailCifrata);
        $stmt->bindParam(':idTipoMetodoRecPsw', $idTipoMetodoRecPsw);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: confermaRecuperoPassword
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('confermaRecuperoPassword')) {
    function confermaRecuperoPassword($idRecPsw, $codice, $nuovaPassword)
    {
        $idUtente = getIdUtenteByIdRecPsw($idRecPsw);


        confrontaConUltimoTentativoDiRecPsw($idRecPsw, $idUtente);
        $idTwoFact = verificaCodiceSecondoFattoreRecuperoPassword($idRecPsw, $codice);
        aggiornoDataUtilizzoCodiceSecondoFattore($idTwoFact);


        cambiaPassword($idUtente, $nuovaPassword);
        invalidoTokenPrecedenti($idUtente);
    }
}

if (!function_exists('cambiaPassword')) {
    function cambiaPassword($idUtente, $password)
    {

        $passwordCifrata = md5(md5($password));

        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_utenti SET dataUltimaModifica = current_timestamp, password = :password WHERE idUtente = :idUtente AND dataEliminazione IS NULL and dataBlocco IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':password', $passwordCifrata);
        $stmt->execute();
    }
}

if (!function_exists('registraUtilizzoCodiceBackup')) {
    function registraUtilizzoCodiceBackup($idUtente, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_codici_backup SET dataUtilizzo = current_timestamp WHERE idUtente = :idUtente AND codice = :codice");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
    }
}

if (!function_exists('verificaCodiceBackup')) {
    function verificaCodiceBackup($idUtente, $codice)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_codici_backup WHERE idUtente = :idUtente AND :codice = :codice AND dataUtilizzo IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }
    }
}



if (!function_exists('getIdUtenteByIdRecPsw')) {
    function getIdUtenteByIdRecPsw($idRecPsw)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_rec_psw WHERE idRecPsw = :idRecPsw AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4");
        $stmt->bindParam(':idRecPsw', $idRecPsw);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Non sono stati trovati tentativi di recupero password ancora in corso di validità, probabilmente hai superato il tempo limite, effettua nuovamente la procedura di recupero password");
        }

        return $result[0]["idUtente"];
    }
}

if (!function_exists('confrontaConUltimoTentativoDiRecPsw')) {
    function confrontaConUltimoTentativoDiRecPsw($idRecPsw, $idUtente)
    {

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idRecPsw FROM " . PREFISSO_TAVOLA . "_rec_psw WHERE idUtente = :idUtente AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4 ORDER BY dataCreazione desc LIMIT 1");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        if ($result[0]["idRecPsw"] != $idRecPsw) {
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Verifica di stare autorizzando il tentativo di recupero password più recente");
        }
    }
}

if (!function_exists('verificaCodiceSecondoFattoreRecuperoPassword')) {
    function verificaCodiceSecondoFattoreRecuperoPassword($idRecPsw, $codice)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTwoFact, tentativi FROM " . PREFISSO_TAVOLA . "_two_fact WHERE idRecPsw = :idRecPsw AND codice = :codice AND dataUtilizzo IS NULL AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4 ");
        $stmt->bindParam(':idRecPsw', $idRecPsw);
        $stmt->bindParam(':codice', $codice);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_two_fact SET tentativi = tentativi + 1 WHERE idRecPsw = :idRecPsw ");
        $stmt->bindParam(':idRecPsw', $idRecPsw);
        $stmt->execute();

        if ($result[0]["tentativi"] > 5){
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Hai superato il numero massimo di tentativi");
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
    }
}

if (!function_exists('invalidoTokenPrecedenti')) {
    function invalidoTokenPrecedenti($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
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

        if (count($result) != 1){
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        return  $result[0]["token"];
    }
}
