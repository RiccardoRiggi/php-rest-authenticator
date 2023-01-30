<?php

if (!function_exists('getMedotoAutenticazionePredefinito')) {
    function getMedotoAutenticazionePredefinito($email)
    {
        try {
            $emailCifrata = cifraStringa($email);

            $conn = apriConnessione();
            $stmt = $conn->prepare("SELECT t.idTipoMetodoLogin as codice, t.descrizione as descrizione FROM " . PREFISSO_TAVOLA . "_utenti u, " . PREFISSO_TAVOLA . "_metodi_login m, " . PREFISSO_TAVOLA . "_t_metodi_login t WHERE u.email = :email AND u.idUtente = m.idUtente AND m.idTipoMetodoLogin = t.idTipoMetodoLogin AND m.isPredefinito = true AND m.dataFineValidita IS NULL AND dataEliminazione IS NULL AND dataBlocco is NULL");
            $stmt->bindParam(':email', $emailCifrata);
            $stmt->execute();
            $result = $stmt->fetchAll();

            if (count($result) > 1) {
                httpErroreServer("Errore durante la ricerca del metodo di autenticazione predefinito");
            } else if (count($result) == 0) {
                $tmp = "EMAIL_PSW_SIX_EMAIL";
                $stmt = $conn->prepare("SELECT idTipoMetodoLogin as codice, descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
                $stmt->bindParam(':idTipoMetodoLogin', $tmp);
                $stmt->execute();
                $result = $stmt->fetchAll();

                http_response_code(200);
                $oggetto = new stdClass();
                $oggetto->codiceMetodoPredefinito = $result[0]["codice"];
                $oggetto->descrizioneMetodoPredefinito = substr($result[0]["descrizione"], 0, strpos($result[0]["descrizione"], "#") - 1);
                exit(json_encode($oggetto));
            } else {
                http_response_code(200);
                $oggetto = new stdClass();
                $oggetto->codiceMetodoPredefinito = $result[0]["codice"];
                $oggetto->descrizioneMetodoPredefinito = substr($result[0]["descrizione"], 0, strpos($result[0]["descrizione"], "#") - 1);
                exit(json_encode($oggetto));
            }
        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione getMedotoAutenticazionePredefinito: " . $e->getMessage());
        }
    }
}

if (!function_exists('effettuaAutenticazione')) {
    function effettuaAutenticazione($email, $password, $tipoAutenticazione)
    {

        if ($tipoAutenticazione == null) {
            try {
                $emailCifrata = cifraStringa($email);
                $passwordCifrata = md5(md5($password));

                generaLogSuFile($emailCifrata);
                generaLogSuFile($passwordCifrata);

                $conn = apriConnessione();
                $stmt = $conn->prepare("SELECT idUtente, nome, cognome FROM " . PREFISSO_TAVOLA . "_utenti WHERE email = :email AND password = :password AND dataEliminazione IS NULL AND dataBlocco is NULL");
                $stmt->bindParam(':email', $emailCifrata);
                $stmt->bindParam(':password', $passwordCifrata);
                $stmt->execute();
                $result = $stmt->fetchAll();

                if (count($result) > 1) {
                    httpErroreServer("Errore durante il processo di autenticazione");
                } else if (count($result) == 0) {
                    httpAccessoNonAutorizzatoLogin();
                } else {
                    $idUtente = $result[0]["idUtente"];
                    $nome = decifraStringa($result[0]["nome"]);
                    $cognome = decifraStringa($result[0]["cognome"]);
                    $idTipoLogin = getMedotoSecondoFattorePreferito($idUtente);
                    $idLogin = inserisciLogin($idUtente, $idTipoLogin);
                    $codice = inserisciCodiceSecondoFattore($idLogin);

                    if ($idTipoLogin == "EMAIL_PSW_SIX_EMAIL") {
                        inviaCodiceSecondoFattoreViaEmail($email, $codice, $nome, $cognome);
                    }

                    $descrizione = getIstruzioniSecondoFattore($idTipoLogin);

                    http_response_code(200);
                    $oggetto = new stdClass();
                    $oggetto->idLogin = $idLogin;
                    $oggetto->descrizione = substr($descrizione, strpos($descrizione, "#") + 2);
                    exit(json_encode($oggetto));
                }
            } catch (Exception $e) {
                generaLogSuFile("Errore nella funzione getMedotoAutenticazionePredefinito: " . $e->getMessage());
                httpErroreServer("Errore durante l'autenticazione");
            }
        }else{

            //TODO Verifico prima esistenza del metodo autenticazione proposto

            //TODO Poi verifico che il metodo di autenticazione proposto esiste per l'utente selezionato

        }
    }
}

if (!function_exists('getIstruzioniSecondoFattore')) {
    function getIstruzioniSecondoFattore($idTipoMetodoLogin)
    {
        try {

            $conn = apriConnessione();
            $stmt = $conn->prepare("SELECT descrizione FROM " . PREFISSO_TAVOLA . "_t_metodi_login WHERE idTipoMetodoLogin = :idTipoMetodoLogin ");
            $stmt->bindParam(':idTipoMetodoLogin', $idTipoMetodoLogin);
            $stmt->execute();
            $result = $stmt->fetchAll();

            return $result[0]["descrizione"];
        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione getIstruzioniSecondoFattore: " . $e->getMessage());
            httpErroreServer("Errore durante la generazione delle istruzioni per inserire codice per il secondo fattore di autenticazione");
        }
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
        try {

            $conn = apriConnessione();
            $stmt = $conn->prepare("SELECT idTipoMetodoLogin FROM " . PREFISSO_TAVOLA . "_metodi_login WHERE idUtente = :idUtente AND dataFineValidita IS NULL AND isPredefinito = true");
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->execute();
            $result = $stmt->fetchAll();

            if (count($result) > 1) {
                httpErroreServer("Errore durante il processo di generazione del codice per il secondo fattore");
            } else if (count($result) == 0) {
                return "EMAIL_PSW_SIX_EMAIL";
            } else {
                return $result[0]["idTipoMetodoLogin"];
            }
        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione getMedotoSecondoFattorePreferito: " . $e->getMessage());
            httpErroreServer("Errore durante la generazione del codice per il secondo fattore di autenticazione");
        }
    }
}

if (!function_exists('inserisciLogin')) {
    function inserisciLogin($idUtente, $idTipoLogin)
    {
        try {


            $idLogin = generaUUID();

            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_login(idLogin, idUtente, idTipoLogin) VALUES (:idLogin, :idUtente, :idTipoLogin)");
            $stmt->bindParam(':idUtente', $idUtente);
            $stmt->bindParam(':idLogin', $idLogin);
            $stmt->bindParam(':idTipoLogin', $idTipoLogin);
            $stmt->execute();

            return $idLogin;
        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione getMedotoSecondoFattorePreferito: " . $e->getMessage());
            httpErroreServer("Errore durante la registrazione del login");
        }
    }
}

if (!function_exists('inserisciCodiceSecondoFattore')) {
    function inserisciCodiceSecondoFattore($idLogin)
    {
        try {

            $idTwoFact = generaUUID();
            $codice = generaCodiceSeiCifre();

            $conn = apriConnessione();
            $stmt = $conn->prepare("INSERT INTO " . PREFISSO_TAVOLA . "_two_fact(idTwoFact, idLogin, codice) VALUES (:idTwoFact, :idLogin, :codice)");
            $stmt->bindParam(':idTwoFact', $idTwoFact);
            $stmt->bindParam(':idLogin', $idLogin);
            $stmt->bindParam(':codice', $codice);
            $stmt->execute();

            return $codice;
        } catch (Exception $e) {
            generaLogSuFile("Errore nella funzione getMedotoSecondoFattorePreferito: " . $e->getMessage());
            httpErroreServer("Errore durante la registrazione del login");
        }
    }
}
