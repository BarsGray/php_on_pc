
<?php

$url = 'https://fem.encar.com/cars/detail/42713434';
// $url = 'https://fem.encar.com/cars/option/42711729';

$ch = curl_init($url);

https://fem.encar.com/cars/detail/41217943?pageid=dc_carsearch&listAdvType=normal&carid=41217943&view_type=hs_ad&adv_attribute=hs_ad&wtClick_korList=019&advClickPosition=kor_normal_p1_g1&tempht_arg=1x3DSI8ivj57_0
https://fem.encar.com/cars/detail/42011724?pageid=dc_carsearch&listAdvType=normal&carid=42011724&view_type=hs_ad&adv_attribute=hs_ad&wtClick_korList=019&advClickPosition=kor_normal_p1_g2&tempht_arg=1x3DSI8ivj57_1

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT =>
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0 Safari/537.36',
]);

$html = curl_exec($ch);
$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

if ($html === false) {
    die('Request error: ' . $error);
}

// Сохраняем ответ для диагностики.
file_put_contents(__DIR__ . '/encar_debug.html', $html);

$data = [
    'source' => 'encar.com',
    'id' => '42713434',
    'requested_url' => $url,
    'final_url' => $finalUrl,
    'http_status' => $status,
    'fetched_at' => date(DATE_ATOM),
    'html_bytes' => strlen($html),
    'title' => null,
    'json_ld' => [],
];

if (preg_match('~<title[^>]*>(.*?)</title>~is', $html, $m)) {
    $data['title'] = html_entity_decode(
        trim(strip_tags($m[1])),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
}

// Извлекаем структурированные данные, если они есть.
if (preg_match_all(
    '~<script\b[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is',
    $html,
    $matches
)) {
    foreach ($matches[1] as $json) {
        $decoded = json_decode(trim($json), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $data['json_ld'][] = $decoded;
        }
    }
}

file_put_contents(
    __DIR__ . '/encar.json',
    json_encode(
        $data,
        JSON_PRETTY_PRINT |
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    ),
    LOCK_EX
);

echo "HTTP status: $status\n";
echo "Final URL: $finalUrl\n";
echo "HTML bytes: " . strlen($html) . "\n";
echo "Title: " . ($data['title'] ?? 'not found') . "\n";
echo "JSON-LD blocks: " . count($data['json_ld']) . "\n";
echo "Saved: encar.json\n";
