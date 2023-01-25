<?php

if (!function_exists('generaLog')) {
    function generaLog($contenuto)
    {
        if (true)
            file_put_contents("svil.log", date("d/m/Y H:i:s") . " - " . $contenuto . "\n", FILE_APPEND);
    }
}