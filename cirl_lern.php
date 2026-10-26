<?php
  // www.che168.com
  // $url = 'https://www.che168.com/dealer/497822/60281764.html#pvareaid=100496#userpid=410000#usercid=410100';

  // $ch = curl_init($url);

  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  // $res   = curl_exec($ch);
  // $error = curl_error($ch);
  // $info  = curl_getinfo($ch);

  // // echo '<pre>';
  // // var_dump($res);
  // // print_r($res);
  // // print_r($error);
  // // print_r($info);
  // // echo '</pre>';
  // echo $res;





// $url = 'https://suchen.mobile.de/fahrzeuge/details.html?id=457174908&utm_campaign=socialbuttons&utm_content=app_ios_vip&utm_medium=social&utm_source=ph.telegra.Telegraph.Share';

// $ch = curl_init($url);

// curl_setopt_array($ch, [
//     CURLOPT_RETURNTRANSFER => true,

//     // SOCKS5-прокси
//     CURLOPT_PROXY => '148.251.68.232:8888',
//     CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,

//     // Таймауты
//     CURLOPT_CONNECTTIMEOUT => 10,
//     CURLOPT_TIMEOUT => 30,

//     // User-Agent браузера
//     CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',

//     // Следовать редиректам
//     CURLOPT_FOLLOWLOCATION => true,
// ]);

// $res = curl_exec($ch);

// $error = curl_error($ch);
// $info = curl_getinfo($ch);

// if ($res === false) {
//     echo 'cURL error: ' . $error;
// } else {
//     echo $res;
// }

// // Для отладки при необходимости:
// // echo '<pre>';
// // print_r($info);
// // echo '</pre>';



$url = 'https://www.che168.com/dealer/497822/60281764.html#pvareaid=100496#userpid=410000#usercid=410100';

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,

    // // SOCKS5-прокси
    // CURLOPT_PROXY => '91.107.248.132:1080',
    // CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,

    // // Таймауты
    // CURLOPT_CONNECTTIMEOUT => 15,
    // CURLOPT_TIMEOUT => 40,

    // TLS
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,

    // // HTTP
    CURLOPT_FOLLOWLOCATION => true,
    // CURLOPT_MAXREDIRS => 5,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    // CURLOPT_HTTPHEADER => [
    //     'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    //     'Accept-Language: de-DE,de;q=0.9,en;q=0.8',
    //     'Connection: close',
    // ],
]);

$res = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);

echo "HTTP status: " . $info['http_code'] . PHP_EOL;

if ($res === false) {
    echo "cURL error: " . $error . PHP_EOL;
} else {
    echo $res;
}
var_dump($res);
