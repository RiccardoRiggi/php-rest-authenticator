<?php

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Funzione: getMetodoAutenticazionePredefinito
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


if (!function_exists('generaIdentificativoDispositivoFisico')) {
    function generaIdentificativoDispositivoFisico()
    {
        verificaValiditaSessione();
        $idUtente = getIdUtenteDaSessione($_SERVER["HTTP_SESSIONE"]);

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
