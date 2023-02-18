<?php
if (!defined("HOST_DATABASE"))
    define("HOST_DATABASE", "localhost");
if (!defined("NOME_DATABASE"))
    define("NOME_DATABASE", "php-rest-authenticator");
if (!defined("USERNAME_DATABASE"))
    define("USERNAME_DATABASE", "root");
if (!defined("PASSWORD_DATABASE"))
    define("PASSWORD_DATABASE", "");
if (!defined("PREFISSO_TAVOLA"))
    define("PREFISSO_TAVOLA", "au");

if (!defined("NOME_APPLICAZIONE"))
    define("NOME_APPLICAZIONE", "PHP Rest Authenticator");

if (!defined("IMPRONTE_SESSIONE_ABILITATE"))
    define("IMPRONTE_SESSIONE_ABILITATE", false);

//Informazioni per la cifratura
if (!defined("CIPHERING_VALUE"))
    define("CIPHERING_VALUE", "AES-128-CTR");
if (!defined("CIPHERING_OPTIONS"))
    define("CIPHERING_OPTIONS", 0);
if (!defined("ENCRYPTION_IV_VALUE"))
    define("ENCRYPTION_IV_VALUE", "4484848594125156");
if (!defined("ENCRYPTION_KEY"))
    define("ENCRYPTION_KEY", "OAC8gRxkG0BVfWstcuRHiGfFN5LKROlwsh9tn3GLweqZF3NoQFRQrI");
