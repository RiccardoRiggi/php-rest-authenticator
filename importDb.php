<?php

include './config.php';
include './utils/logUtils.php';
include './utils/cifraturaUtils.php';
include './database/database.php';
include './utils/httpResponseCodeUtils.php';

if (ABILITA_MODALITA_IMPORT_DB) {
    generaLogSuBaseDati("INFO", "Modalità import DB abilitata!");


    $lines = file('./configurazioneDatabase/import.sql');
    $count = 0;
    $errors = 0;

    $conn = apriConnessione();

    foreach ($lines as $line) {
        $count += 1;
        try {
            $stmt = $conn->prepare($line);
            $stmt->execute();
        } catch (Exception $e) {
            $errors += 1;
            generaLogSuBaseDati("ERROR", "Errore durante l'inserimento della seguente configurazione: " . $line);
            generaLogSuBaseDati("ERROR", $e->getMessage());
        }
    }
    chiudiConnessione($conn);
    generaLogSuBaseDati("INFO", "Sono andate in errore " . $errors . " su " . $count . " configurazioni");
} else {
    generaLogSuBaseDati("INFO", "Modalità import DB NON abilitata. Ricordati di cancellare il file importDb.php se non più necessario");
}
