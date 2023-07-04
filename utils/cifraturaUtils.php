<?php


if (!function_exists('cifraStringa')) {
    function cifraStringa($stringaDaCifrare)
    {
        $stringaCifrata = openssl_encrypt($stringaDaCifrare, CIPHERING_VALUE, ENCRYPTION_KEY, CIPHERING_OPTIONS, ENCRYPTION_IV_VALUE);
        generaLogSuFile($stringaCifrata);
        return $stringaCifrata;
    }
}

if (!function_exists('decifraStringa')) {
    function decifraStringa($stringaCifrata)
    {
        $stringaDecifrata = openssl_decrypt($stringaCifrata, CIPHERING_VALUE, ENCRYPTION_KEY, CIPHERING_OPTIONS,ENCRYPTION_IV_VALUE);
        generaLogSuFile($stringaDecifrata);
        return $stringaDecifrata;
    }
}

if(!function_exists("generaUUID")){
    function generaUUID(){
        return uniqid("",true)."-".uniqid("",true);
    }
}

if(!function_exists("generaCodiceSeiCifre")){
    function generaCodiceSeiCifre(){
        return rand(100000,999999);
    }
}

