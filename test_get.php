<?php
$ch = curl_init('https://fitlife-auth-service.onrender.com/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: " . $httpcode . "\n";
echo substr($response, 0, 500);
