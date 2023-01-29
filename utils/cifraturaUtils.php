<?php


if (!function_exists('cifraStringa')) {
    function cifraStringa($stringaDaCifrare)
    {
        $stringaCifrata = openssl_encrypt($stringaDaCifrare, CIPHERING_VALUE, ENCRYPTION_KEY, CIPHERING_OPTIONS, ENCRYPTION_IV_VALUE);
        generaLog($stringaCifrata);
        return $stringaCifrata;
    }
}

if (!function_exists('decifraStringa')) {
    function decifraStringa($stringaCifrata)
    {
        $stringaDecifrata = openssl_decrypt($stringaCifrata, CIPHERING_VALUE, ENCRYPTION_KEY, CIPHERING_OPTIONS,ENCRYPTION_IV_VALUE);
        generaLog($stringaDecifrata);
        return $stringaDecifrata;
    }
}
