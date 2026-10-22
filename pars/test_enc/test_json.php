<?php

$url = 'https://api.encar.com/v1/readside/vehicle/42978852?include=ADVERTISEMENT,CATEGORY,CONDITION,CONTACT,MANAGE,OPTIONS,PHOTOS,SPEC,PARTNERSHIP,CENTER,VIEW';

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0 Safari/537.36',
    CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Referer: https://fem.encar.com/'],
]);

$response = curl_exec($ch);
$error    = curl_error($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

// Проверка ошибки cURL
if ($response === false) {
    http_response_code(502);
    die('Request error: ' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8'));
}

// Проверка HTTP-статуса
if ($status < 200 || $status >= 300) {
    http_response_code(502);

    echo "HTTP status: $status\n";
    echo "Final URL: $finalUrl\n";
    echo "\nAPI response:\n";
    echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');

    exit;
}

// Декодируем JSON
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(502);
    die('JSON decode error: ' . htmlspecialchars(json_last_error_msg(), ENT_QUOTES, 'UTF-8'));
}

// Форматируем JSON для вывода
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($json === false) die('JSON encode error: ' . json_last_error_msg());

// Вывод JSON в браузере
header('Content-Type: application/json; charset=utf-8');

echo $json;
