<?php
  // $url = 'https://delica.testvzh.ru/catalog/bytovaya-produktsiya/';

  // $ch = curl_init($url);

  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  // $response = curl_exec($ch);

  // curl_close($ch);

  // header('Content-Type: text/plain; charset=utf-8');

  // echo $response;

$url = 'https://delica.testvzh.ru/catalog/bytovaya-produktsiya/';

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);

$error = curl_error($ch);
$info = curl_getinfo($ch);

curl_close($ch);

// echo '<pre>';
// echo htmlspecialchars($response, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
// echo '</pre>';



// if (preg_match('~<title[^>]*>(.*?)</title>~is', $response, $matches)) {
//     echo 'Заголовок страницы: ' . trim($matches[1]);
// } else {
//     echo 'Заголовок не найден';
// }

if (preg_match_all( '~<[^>]*class=["\'][^"\']*\bcatalog_item_name\b[^"\']*["\'][^>]*>(.*?)</[^>]+>~si', $response, $matches )) {
  foreach ($matches[1] as $name) {
    $name = trim(strip_tags($name));

    $name = html_entity_decode( $name, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    if ($name !== '') { echo htmlspecialchars( $name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . "<br>\n"; }
  }
} else {
  echo 'Товары не найдены';
}