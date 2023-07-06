<?php

include './importManager.php';
include '../services/webHookTelegramService.php';


$jsonBodyString = @file_get_contents("php://input");
$jsonBody = json_decode($jsonBodyString, TRUE);

$idRichiestaAccesso = "";

try {
    if (isset($jsonBody['message'])) {
        $idTelegramMittente = $jsonBody['message']['from']['id'];
        $username = $jsonBody['message']['from']['username'] . " - " . $jsonBody['message']['from']['last_name'] . " " . $jsonBody['message']['from']['first_name'];
        $nazione = $jsonBody['message']['from']['language_code'];
        $messaggio = $jsonBody["message"]['text'];
    }
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-01 - Errore imprevisto");
    echo "CODE-01 - Errore imprevisto";
    return;
}

try {
    generaLogTelegram($idTelegramMittente, $jsonBodyString);
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-02 - Errore imprevisto");
    return;
}

//VERIFICO SE IDMITTENTE NON ESISTE OPPURE ESISTE OPPURE ESISTE ED è BLOCCATO
try {
    if (!isIdTelegramTempEsistente($idTelegramMittente)) {
        inserisciIdTelegramTmp($idTelegramMittente);
    }
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-03 - Errore imprevisto");
    return;
}

try {
    if (isTelegramIdBloccato($idTelegramMittente)) {
        inviaNotificaTelegram($idTelegramMittente, "Accesso non autorizzato");
        return;
    }
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-04 - Errore imprevisto");
    return;
}


//ASSOCIAZIONE ACCOUNT TELEGRAM
if (str_starts_with($messaggio, "T-") && strlen($messaggio) == 8) {
    try {
        if (!isCodiceAssociazioneEsistente($messaggio)) {
            incrementaContatoreAlertTelegram($idTelegramMittente);
            incrementaContatoreAlertTelegram("TEMP" . $idTelegramMittente);
            inviaNotificaTelegram($idTelegramMittente, "Verifica di aver digitato correttamente il codice. Se ti sembra corretto, ma è passato troppo tempo da quando è stato generato, prova a ripetere la procedura");
            return;
        } else {
            associaDispositivoTelegram($idTelegramMittente, $nazione, $username, $messaggio);
            inviaNotificaTelegram($idTelegramMittente, "Dispositivo associato con successo");
            return;
        }
    } catch (Exception $e) {
        inviaNotificaTelegram($idTelegramMittente, "La procedura non è andata a buon fine");
        return;
    }
}
//FINE ASSOCIAZIONE ACCOUNT TELEGRAM



try {
    incrementaContatoreAlertTelegram($idTelegramMittente);
    return;
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-05 - Errore imprevisto");
    return;
}

try {
    incrementaContatoreAlertTelegram("TEMP" . $idTelegramMittente);
    inviaNotificaTelegram($idTelegramMittente, "Comando non riconosciuto");
    return;
} catch (Exception $e) {
    inviaNotificaTelegram($idTelegramMittente, "CODE-06 - Errore imprevisto");
    return;
}

inviaNotificaTelegram($idTelegramMittente, "Comando sconosciuto");