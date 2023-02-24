<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getMetodoAutenticazionePredefinito
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('generaIdentificativoDispositivoFisico')) {
    function generaIdentificativoDispositivoFisico()
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $idDispositivoFisico = generaUUID();

        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_dispositivi_fisici (idDispositivoFisico, idUtente, dataAbilitazione) VALUES (:idDispositivoFisico, :idUtente, NULL)");
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();

        $oggetto = new stdClass();
        $oggetto->idDispositivoFisico = $idDispositivoFisico;
        return $oggetto;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: abilitaDispositivoFisico
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('abilitaDispositivoFisico')) {
    function abilitaDispositivoFisico($idDispositivoFisico, $nomeDispositivo)
    {
        isDispositivoDaAbilitare($idDispositivoFisico);
        $idUtente = getIdUtenteByDispositivo($idDispositivoFisico);
        disabilitaDispositivi($idUtente);
        abilitaDispositivo($idDispositivoFisico, $nomeDispositivo);
    }
}

if (!function_exists('isDispositivoDaAbilitare')) {
    function isDispositivoDaAbilitare($idDispositivoFisico)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idDispositivoFisico FROM " . PREFISSO_TAVOLA . "_dispositivi_fisici WHERE idDispositivoFisico = :idDispositivoFisico AND dataAbilitazione IS NULL");
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();
    }
}

if (!function_exists('getIdUtenteByDispositivo')) {
    function getIdUtenteByDispositivo($idDispositivoFisico)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_dispositivi_fisici WHERE idDispositivoFisico = :idDispositivoFisico ");
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return $result[0]["idUtente"];
    }
}

if (!function_exists('disabilitaDispositivi')) {
    function disabilitaDispositivi($idUtente)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_dispositivi_fisici SET dataDisabilitazione = current_timestamp WHERE idUtente = :idUtente AND dataAbilitazione IS NOT NULL ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
    }
}

if (!function_exists('abilitaDispositivo')) {
    function abilitaDispositivo($idDispositivoFisico, $nomeDispositivo)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_dispositivi_fisici SET dataAbilitazione = current_timestamp, nomeDispositivo = :nomeDispositivo WHERE idDispositivoFisico = :idDispositivoFisico ");
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->bindParam(':nomeDispositivo', $nomeDispositivo);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: disabilitaDispositivoFisico
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('disabilitaDispositivoFisico')) {
    function disabilitaDispositivoFisico($idDispositivoFisico)
    {
        verificaValiditaToken();
        $idUtenteToken = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);
        $idUtenteDispositivo = getIdUtenteByDispositivo($idDispositivoFisico);

        if ($idUtenteToken != $idUtenteDispositivo)
            throw new AccessoNonAutorizzatoException();

        disabilitaDispositivo($idUtenteToken, $idDispositivoFisico);
    }
}

if (!function_exists('disabilitaDispositivo')) {
    function disabilitaDispositivo($idUtente, $idDispositivoFisico)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("UPDATE " . PREFISSO_TAVOLA . "_dispositivi_fisici SET dataDisabilitazione = current_timestamp WHERE idUtente = :idUtente AND idDispositivoFisico = :idDispositivoFisico AND dataAbilitazione IS NOT NULL ");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->execute();
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getDispositiviFisici
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getDispositiviFisici')) {
    function getDispositiviFisici()
    {
        verificaValiditaToken();
        $idUtente = getIdUtenteDaToken($_SERVER["HTTP_TOKEN"]);

        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT nomeDispositivo, dataAbilitazione, dataDisabilitazione FROM " . PREFISSO_TAVOLA . "_dispositivi_fisici WHERE idUtente = :idUtente ORDER BY dataAbilitazione DESC");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getRichiesteDiAccessoPendenti
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('getRichiesteDiAccessoPendenti')) {
    function getRichiesteDiAccessoPendenti($idDispositivoFisico)
    {

        $idUtente = getIdUtenteByDispositivo($idDispositivoFisico);
        return getAccessiPendenti($idUtente);
    }
}

if (!function_exists('getAccessiPendenti')) {
    function getAccessiPendenti($idUtente)
    {


        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT f.idTwoFact, l.idTipoLogin, f.codice, f.dataCreazione, l.token, f.dataUtilizzo, TIMESTAMPDIFF(MINUTE,f.dataCreazione,NOW())  as tempoPassato, f.indirizzoIp  FROM " . PREFISSO_TAVOLA . "_login l, " . PREFISSO_TAVOLA . "_two_fact f WHERE l.idUtente = :idUtente AND f.idLogin = l.idLogin AND TIMESTAMPDIFF(MINUTE,f.dataCreazione,NOW()) < 4 ORDER BY f.dataCreazione DESC LIMIT 1");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $array = [];
        foreach ($result as $value) {
            $tmp = $value;
            $tmp["indirizzoIp"] = decifraStringa($value["indirizzoIp"]);
            $tmp["5"] = decifraStringa($value["5"]);
            array_push($array, $tmp);
        }
        return $array;
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: autorizzaAccesso
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('autorizzaAccesso')) {
    function autorizzaAccesso($idDispositivoFisico, $idTwoFact)
    {

        $idUtenteDaDispositivo = getIdUtenteByDispositivo($idDispositivoFisico);
        $idUtenteTwoFact = getIdUtenteByIdTwoFact($idTwoFact);

        if ($idUtenteDaDispositivo != $idUtenteTwoFact)
            throw new AccessoNonAutorizzatoLoginException();

        $login = getIdLoginByIdTwoFact($idTwoFact);
        aggiornoDataUtilizzoCodiceSecondoFattore($idTwoFact);
        invalidoTokenPrecedenti($idUtenteDaDispositivo);
        $token = registraTokenQrCode($login["idLogin"], $idUtenteDaDispositivo, $login["userAgent"], $login["indirizzoIp"]);
        aggiornoLoginConToken($login["idLogin"], $token);
    }
}

if (!function_exists('getIdUtenteByIdTwoFact')) {
    function getIdUtenteByIdTwoFact($idTwoFact)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idUtente FROM " . PREFISSO_TAVOLA . "_login l, " . PREFISSO_TAVOLA . "_two_fact f WHERE f.idLogin = l.idLogin AND f.idTwoFact = :idTwoFact AND f.dataUtilizzo IS NULL AND l.token IS NULL AND TIMESTAMPDIFF(MINUTE,l.dataCreazione,NOW()) < 4");
        $stmt->bindParam(':idTwoFact', $idTwoFact);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

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

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: autorizzaQrCode
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('autorizzaQrCode')) {
    function autorizzaQrCode($idDispositivoFisico, $idQrCode)
    {
        $idUtenteDaDispositivo = getIdUtenteByDispositivo($idDispositivoFisico);
        $datiChiamante = getDatiChiamanteByQrCode($idQrCode);
        invalidoTokenPrecedenti($idUtenteDaDispositivo);
        $token = registraTokenQrCode($idQrCode, $idUtenteDaDispositivo, $datiChiamante["userAgent"], $datiChiamante["indirizzoIp"]);
        inserisciLoginDaQrCode($idQrCode, $idUtenteDaDispositivo, $token);
    }
}

if (!function_exists('inserisciLoginDaQrCode')) {
    function inserisciLoginDaQrCode($idLogin, $idUtente, $token)
    {
        $idLogin = generaUUID();
        $conn = apriConnessione();
        $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_login(idLogin, idUtente, idTipoLogin,token) VALUES (:idLogin, :idUtente, 'QR_CODE' ,:token)");
        $stmt->bindParam(':idUtente', $idUtente);
        $stmt->bindParam(':idLogin', $idLogin);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $idLogin;
    }
}



if (!function_exists('getDatiChiamanteByQrCode')) {
    function getDatiChiamanteByQrCode($idQrCode)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT indirizzoIp, userAgent FROM " . PREFISSO_TAVOLA . "_qr_code WHERE idQrCode = :idQrCode ");
        $stmt->bindParam(':idQrCode', $idQrCode);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) != 1)
            throw new AccessoNonAutorizzatoLoginException();

        return $result[0];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: isDispositivoAbilitato
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('isDispositivoAbilitato')) {
    function isDispositivoAbilitato($idDispositivoFisico)
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT idDispositivoFisico FROM " . PREFISSO_TAVOLA . "_dispositivi_fisici WHERE idDispositivoFisico = :idDispositivoFisico AND dataAbilitazione IS NOT NULL and dataDisabilitazione IS NULL ORDER BY dataAbilitazione DESC");
        $stmt->bindParam(':idDispositivoFisico', $idDispositivoFisico);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return count($result) == 1;
    }
}
