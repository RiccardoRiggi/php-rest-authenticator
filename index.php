<?php

include './utils/logUtils.php';
include './utils/cifraturaUtils.php';

http_response_code(403);
//header('Content-Type: application/json; charset=utf-8');

//generaLog(json_encode($_SERVER));
//echo json_encode($_SERVER);

$original_string = "Welcome to JavaTpoint advance ggrtgtrhr \n";  
// Print the original input string  
echo "Original String: " .$original_string;  
// Store the cipher method   
// Store the encryption key  
// Use openssl_encrypt() function   
//$encryption_value = openssl_encrypt($original_string, $ciphering_value, $encryption_key, $options,$encryption_iv_value);  
$encryption_value = cifraStringa($original_string);
// Display the encrypted input string data  
echo "<br><br> Encrypted Input String: " . $encryption_value  . "\n";  
$decryption_key = "JavaTpoint";  
// Use openssl_decrypt() function to decrypt the data  
$decryption_value = decifraStringa($encryption_value);   
// Display the decrypted string as an original data  
echo "<br><br> Decrypted Input String: " .$decryption_value. "\n";  
