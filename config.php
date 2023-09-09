<?php

//Informazioni per collegare il db
if (!defined("HOST_DATABASE"))
    define("HOST_DATABASE", "localhost");
if (!defined("NOME_DATABASE"))
    define("NOME_DATABASE", "php-rest-authenticator");
if (!defined("USERNAME_DATABASE"))
    define("USERNAME_DATABASE", "root");
if (!defined("PASSWORD_DATABASE"))
    define("PASSWORD_DATABASE", "");

/*
Prefisso delle tavole, in questo modo è possibile avere più installazioni su un unico db. 
Dovrai cambiare i prefissi dalle tavole manualmente, il default è "au"
*/
if (!defined("PREFISSO_TAVOLA"))
    define("PREFISSO_TAVOLA", "au");

if (!defined("NOME_APPLICAZIONE"))
    define("NOME_APPLICAZIONE", "PHP Rest Authenticator");

/*
Questa variabile contiene il numero di elementi che verranno restituiti per ogni pagina nelle liste in cui è implementata
la paginazione
*/
if (!defined("ELEMENTI_PER_PAGINA"))
    define("ELEMENTI_PER_PAGINA", 10);

/*
Questa variabile di configurazione abilita la verifica del token e degli eventuali permessi per accedere a determinate risorse.
Non impostarlo a false per nessun motivo se stai utilizzando il software su rete pubblica
*/
if (!defined("ABILITA_VERIFICA_TOKEN"))
    define("ABILITA_VERIFICA_TOKEN", true);

/*
Questa variabile di configurazione abilita il controllo dello stesso indirizzo ip con il quale è stato generato il token    
*/
if (!defined("ABILITA_VERIFICA_STESSO_INDIRIZZO_IP"))
    define("ABILITA_VERIFICA_STESSO_INDIRIZZO_IP", true);

/*
Questa variabile di configurazione abilita il controllo dello stesso User Agent con il quale è stato generato il token    
*/
if (!defined("ABILITA_VERIFICA_STESSO_USER_AGENT"))
    define("ABILITA_VERIFICA_STESSO_USER_AGENT", true);

/*
Questa variabile serve per abilitare i cors se FE e BE si trovano su host differenti
*/
if (!defined("ABILITA_CORS"))
    define("ABILITA_CORS", true);

/*
Questa variabile serve per abilitare l'invio delle email
*/
if (!defined("ABILITA_INVIO_EMAIL"))
    define("ABILITA_INVIO_EMAIL", false);

/*
Questa variabile serve per abilitare anche il log su file
*/
if (!defined("ABILITA_LOG_FILE"))
    define("ABILITA_LOG_FILE", false);

//Informazioni per la cifratura, da non cambiare dopo l'installazione
if (!defined("CIPHERING_VALUE"))
    define("CIPHERING_VALUE", "AES-128-CTR");
if (!defined("CIPHERING_OPTIONS"))
    define("CIPHERING_OPTIONS", 0);
if (!defined("ENCRYPTION_IV_VALUE"))
    define("ENCRYPTION_IV_VALUE", "4484848594125156");
if (!defined("ENCRYPTION_KEY"))
    define("ENCRYPTION_KEY", "OAC8gRxkG0BVfWstcuRHiGfFN5LKROlwsh9tn3GLweqZF3NoQFRQrI");

/*
Variabili per la gestione del BOT Telegram
*/
if (!defined("NOME_BOT_TELEGRAM"))
    define("NOME_BOT_TELEGRAM", "");
if (!defined("TOKEN_TELEGRAM"))
    define("TOKEN_TELEGRAM", "");
