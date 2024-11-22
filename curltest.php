<?php
$ch = curl_init('https://bridgetechproperties.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    echo 'Success!';
}
curl_close($ch);
print_r(curl_version()['ssl_version']);
var_dump(openssl_get_cert_locations());
