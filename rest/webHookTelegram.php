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
    } else if (isset($jsonBody['callback_query'])) {
        $idTelegramMittente = $jsonBody['callback_query']['from']['id'];
        $username = $jsonBody['callback_query']['from']['username'] . " - " . $jsonBody['callback_query']['from']['last_name'] . " " . $jsonBody['callback_query']['from']['first_name'];
        $nazione = $jsonBody['callback_query']['from']['language_code'];
        $messaggio = $jsonBody["callback_query"]['message']['text'];
        $idRichiestaAccesso = $jsonBody["callback_query"]["message"]["reply_markup"]['inline_keyboard'][0][0]["callback_data"];
    }
} catch (Exception $e) {
    echo "CODE-01 - Errore imprevisto";
    return;
}

try {
    generaLogTelegram($idTelegramMittente, $jsonBodyString);
} catch (Exception $e) {
    echo "CODE-02 - Errore imprevisto";
    return;
}

//VERIFICO SE IDMITTENTE NON ESISTE OPPURE ESISTE OPPURE ESISTE ED è BLOCCATO
try {
    if (!isIdTelegramTempEsistente($idTelegramMittente)) {
        inserisciIdTelegramTmp($idTelegramMittente);
    }
} catch (Exception $e) {
    echo "CODE-03 - Errore imprevisto";
    return;
}

try {
    if (isTelegramIdBloccato($idTelegramMittente)) {
        echo "Accesso non autorizzato";
        return;
    }
} catch (Exception $e) {
    echo "CODE-04 - Errore imprevisto";
    return;
}


//ASSOCIAZIONE ACCOUNT TELEGRAM
if (str_starts_with($messaggio, "T-") && strlen($messaggio) == 8) {
    try {
        if (!isCodiceAssociazioneEsistente($messaggio)) {
            incrementaContatoreAlertTelegram($idTelegramMittente);
            incrementaContatoreAlertTelegram("TEMP" . $idTelegramMittente);
            echo "Verifica di aver digitato correttamente il codice. Se ti sembra corretto, ma è passato troppo tempo da quando è stato generato, prova a ripetere la procedura";
            return;
        } else {
            associaDispositivoTelegram($idTelegramMittente, $nazione, $username, $messaggio);
            echo "Dispositivo associato con successo!";
            return;
        }
    } catch (Exception $e) {
        echo "La procedura di associazione non è andata a buon fine";
        return;
    }
}
//FINE ASSOCIAZIONE ACCOUNT TELEGRAM





//RECUPERO RICHIESTA AUTORIZZAZIONE AUTENTICAZIONE
if (str_starts_with($idRichiestaAccesso, "AUTORIZZA-")) {
    try {
        autorizzaAccessoTelegram($idTelegramMittente, str_replace("AUTORIZZA-", "", $idRichiestaAccesso));
        echo "Procedura conclusa con successo";
        return;
    } catch (Exception $e) {
        echo "La procedura di autorizzazione non è andata a buon fine";
        return;
    }
}
//FINE RECUPERO RICHIESTA AUTORIZZAZIONE AUTENTICAZIONE

try {
    incrementaContatoreAlertTelegram($idTelegramMittente);
    return;
} catch (Exception $e) {
    echo "CODE-05 - Errore imprevisto";
    return;
}

try {
    incrementaContatoreAlertTelegram("TEMP" . $idTelegramMittente);
    echo "Comando non riconosciuto";
    return;
} catch (Exception $e) {
    echo "CODE-06 - Errore imprevisto";
    return;
}

echo "Comando non riconosciuto";
