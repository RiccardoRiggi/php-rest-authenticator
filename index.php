<?php

include './config.php';
include './utils/logUtils.php';
include './utils/cifraturaUtils.php';
include './database/database.php';
include './utils/httpResponseCodeUtils.php';

//http_response_code(403);
//header('Content-Type: application/json; charset=utf-8');

//generaLog(json_encode($$_REQUEST));
//echo json_encode($_SERVER);

//generaLog(json_decode(file_get_contents('php://input'), true));

//$data = json_decode(file_get_contents('php://input'), true);
//echo "".$data[0]["nome"];

//echo cifraStringa("AAAAAAA");

//echo file_get_contents('php://input');


//generaLogOperazioni("SESSIONE","MODIFICATO UTENTE");

//httpAccessoNonAutorizzato();

//Nome
echo "Riccardo"." ".cifraStringa("Riccardo");
echo "<br/>";
//Cognome
echo "Riggi"." ".cifraStringa("Riggi");
echo "<br/>";
//Email
echo "info@riccardoriggi.it"." ".cifraStringa("info@riccardoriggi.it");
echo "<br/>";
//Password
echo "123456"." ".md5(md5("123456"));
echo "<br/>";