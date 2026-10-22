
<?php

$url = 'https://delica.testvzh.ru/catalog/bytovaya-produktsiya/';

$ch = curl_init($url);

curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30]);

$response = curl_exec($ch);

$error = curl_error($ch);
$info = curl_getinfo($ch);

curl_close($ch);

libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">' . $response);

$xpath = new DOMXPath($dom);

// Находим все карточки товаров
$items = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' catalog_item ')]");

foreach ($items as $item) {

    // Находим название товара
    $nameNode = $xpath->query(".//p[contains(concat(' ', normalize-space(@class), ' '), ' catalog_item_name ')]", $item)->item(0);

    // Находим ссылку на товар
    $linkNode = $xpath->query(".//a[@href]", $item)->item(0);

    if ($nameNode && $linkNode) {
        $name = trim($nameNode->textContent);
        $link = $linkNode->getAttribute('href');

        echo 'Название: ' . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
        echo 'Ссылка: ' . htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br><hr>';

        $products[] = ['name' => $name, 'url'  => $link];
    }
}

// Преобразуем массив PHP в JSON
$json = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

if ($json === false) {die('Ошибка создания JSON: ' . json_last_error_msg());}

// Записываем JSON в файл
$file = __DIR__ . '/products.json';

if (file_put_contents($file, $json . PHP_EOL, LOCK_EX) === false) {die('Не удалось записать файл JSON');}

libxml_clear_errors();

echo 'Готово! Сохранено товаров: ' . count($products);
echo '<br>Файл: products.json';

