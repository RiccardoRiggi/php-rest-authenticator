<?php

if (!function_exists('incrementaContatoreAlertTelegram')) {
    function incrementaContatoreAlertTelegram($idTelegram)
    {

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_telegram SET contatoreAlert = contatoreAlert + 1 WHERE idTelegram = :idTelegram";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        chiudiConnessione($conn);


        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT contatoreAlert FROM " . PREFISSO_TAVOLA . "_telegram WHERE idTelegram = :idTelegram");
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) > 0 && $result[0]["contatoreAlert"] > 10) {
            $sql = "UPDATE " . PREFISSO_TAVOLA . "_telegram SET dataBlocco = CURRENT_TIMESTAMP WHERE idTelegram = :idTelegram";
            $conn = apriConnessione();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idTelegram', $idTelegram);
            $stmt->execute();
            chiudiConnessione($conn);
        }
    }
}

if (!function_exists('isIdTelegramTempEsistente')) {
    function isIdTelegramTempEsistente($idTelegram)
    {
        $idTelegram = "TEMP" . $idTelegram;
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTelegram FROM " . PREFISSO_TAVOLA . "_telegram WHERE idTelegram = :idTelegram");
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        return count($result) == 1;
    }
}

if (!function_exists('isIdTelegramEsistente')) {
    function isIdTelegramEsistente($idTelegram)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTelegram FROM " . PREFISSO_TAVOLA . "_telegram WHERE idTelegram = :idTelegram");
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        return count($result) == 1;
    }
}

if (!function_exists('isCodiceAssociazioneEsistente')) {
    function isCodiceAssociazioneEsistente($codiceAssociazione)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT codiceAssociazione FROM " . PREFISSO_TAVOLA . "_telegram WHERE codiceAssociazione = :codiceAssociazione AND dataBlocco IS NULL AND dataDisabilitazione IS NULL AND dataAbilitazione IS NULL AND TIMESTAMPDIFF(MINUTE,dataCreazione,NOW()) < 4");
        $stmt->bindParam(':codiceAssociazione', $codiceAssociazione);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        return count($result) == 1;
    }
}

if (!function_exists('associaDispositivoTelegram')) {
    function associaDispositivoTelegram($idTelegram, $nazione, $usernameTelegram, $codiceAssociazione)
    {

        $sql = "UPDATE " . PREFISSO_TAVOLA . "_telegram SET idTelegram = :idTelegram, nazione = :nazione, usernameTelegram = :usernameTelegram, dataAbilitazione = CURRENT_TIMESTAMP WHERE codiceAssociazione = :codiceAssociazione AND idTelegram = idDispositivoFisico";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->bindParam(':nazione', $nazione);
        $stmt->bindParam(':usernameTelegram', $usernameTelegram);
        $stmt->bindParam(':codiceAssociazione', $codiceAssociazione);
        $stmt->execute();
        chiudiConnessione($conn);

        $sql = "SELECT idUtente FROM " . PREFISSO_TAVOLA . "_telegram WHERE idTelegram = :idTelegram AND dataBlocco IS NULL";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) > 0) {
            $idUtente = $result[0]["idUtente"];
            $sql = "UPDATE " . PREFISSO_TAVOLA . "_telegram SET dataDisabilitazione = CURRENT_TIMESTAMP WHERE idUtente = :idUtente AND idTelegram != :idTelegram AND dataDisabilitazione IS NULL";
            $conn = apriConnessione();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idTelegram', $idTelegram);
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->execute();
            chiudiConnessione($conn);
        }
    }
}

if (!function_exists('inserisciIdTelegramTmp')) {
    function inserisciIdTelegramTmp($idTelegramProvvisorio)
    {
        $idTelegramProvvisorio = "TEMP" . $idTelegramProvvisorio;
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_telegram (idTelegram,  idDispositivoFisico) VALUES (:idTelegram, :idDispositivoFisico)");
        $stmt->bindParam(':idTelegram', $idTelegramProvvisorio);
        $stmt->bindParam(':idDispositivoFisico', $idTelegramProvvisorio);


        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('isTelegramIdBloccato')) {
    function isTelegramIdBloccato($idTelegram)
    {
        $idTelegramTmp = "TEMP" . $idTelegram;
        $sql = "SELECT idTelegram FROM " . PREFISSO_TAVOLA . "_telegram WHERE ( idTelegram = :idTelegram OR idTelegram = :idTelegramTmp ) AND dataBlocco IS NOT NULL";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->bindParam(':idTelegramTmp', $idTelegramTmp);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        return count($result) > 1;
    }
}

if (!function_exists('getIdUtenteByTelegram')) {
    function getIdUtenteByTelegram($idTelegram)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_telegram WHERE idTelegram = :idTelegram AND dataDisabilitazione IS NULL AND dataAbilitazione IS NOT NULL AND dataBlocco IS NULL");
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1) {
            throw new AccessoNonAutorizzatoLoginException();
        }

        return $result[0]["idUtente"];
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

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new OtterGuardianException(401, "Non sono stati trovati tentativi di accesso ancora in corso di validità, probabilmente hai superato il tempo limite, effettua nuovamente la procedura di autenticazione");
        }

        return $result[0]["idUtente"];
    }
}

if (!function_exists('getIdLoginByIdTwoFact')) {
    function getIdLoginByIdTwoFact($idTwoFact)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT f.idLogin as idLogin, l.userAgent as userAgent, l.indirizzoIp as indirizzoIp FROM " . PREFISSO_TAVOLA . "_login l, " . PREFISSO_TAVOLA . "_two_fact f WHERE f.idLogin = l.idLogin AND f.idTwoFact = :idTwoFact AND TIMESTAMPDIFF(MINUTE,l.dataCreazione,NOW()) < 4");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1) {
            incrementaContatoreAlert();
            throw new AccessoNonAutorizzatoLoginException();
        }

        return $result[0];
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
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_token SET dataFineValidita = current_timestamp WHERE idUtente = :idUtente ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        chiudiConnessione($conn);
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




if (!function_exists('inviaNotificaTelegram')) {
    function inviaNotificaTelegram($idTelegram, $testo)
    {
        $url = "https://api.telegram.org/bot" . TOKEN_TELEGRAM . "/sendMessage?chat_id=" . $idTelegram . "&parse_mode=HTML&text=" . urlencode($testo);

        @file_get_contents($url);

        inserisciLogNotificaTelegram($idTelegram,$url);
    }
}

if (!function_exists('inserisciLogNotificaTelegram')) {
    function inserisciLogNotificaTelegram($idTelegram, $testo)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_notifiche_telegram(idTelegram, dataInvio,testo) VALUES ( :idTelegram, current_timestamp, :testo)");
        $stmt->bindParam(':idTelegram', $idTelegram);
        $stmt->bindParam(':testo', $testo);
        $stmt->execute();
        chiudiConnessione($conn);

        
    }
}

if (!function_exists('getIdTelegramByIdUtente')) {
    function getIdTelegramByIdUtente($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idTelegram FROM " . PREFISSO_TAVOLA . "_telegram WHERE idUtente = :idUtente AND dataDisabilitazione IS NULL AND dataAbilitazione IS NOT NULL AND dataBlocco IS NULL");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);

        if (count($result) != 1) {
            throw new AccessoNonAutorizzatoLoginException();
        }

        return $result[0]["idTelegram"];
    }
}
