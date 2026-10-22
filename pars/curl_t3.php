
<?php

libxml_use_internal_errors(true);

$inputFile  = __DIR__ . '/products.json';
$outputFile = __DIR__ . '/products_details.json';

// Читаем список товаров
$json = file_get_contents($inputFile);

if ($json === false) {die('Не удалось прочитать products.json');}

$products = json_decode($json, true);

if (!is_array($products)) {die('Ошибка JSON: ' . json_last_error_msg());}

// Функция для получения HTML страницы через cURL
function getPage(string $url): array
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30, CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; ProductParser/1.0)']);

    $html = curl_exec($ch);
    $error = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($html === false || $status < 200 || $status >= 300) {
        return ['success' => false,'error' => $error ?: "HTTP $status",];
    }
    return ['success' => true,'html' => $html,];
}

// Извлекаем данные из карточки товара
function parseProduct(string $html): array
{
    $dom = new DOMDocument();

    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);

    $xpath = new DOMXPath($dom);

    // Название товара: h1.title
    $titleNode = $xpath->query("//h1[contains(concat(' ', normalize-space(@class), ' '), ' title ')]")->item(0);

    $title = $titleNode ? trim($titleNode->textContent) : '';

    // Характеристики: .param_name и .param_value
    $params = [];

    $paramItems = $xpath->query("//li[contains(concat(' ', normalize-space(@class), ' '), ' params_item ')]");

    foreach ($paramItems as $item) {
        $nameNode = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' param_name ')]", $item)->item(0);

        $valueNode = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' param_value ')]", $item)->item(0);

        if ($nameNode && $valueNode) {
            $name = trim($nameNode->textContent);
            $name = rtrim($name, " \t\n\r\0\x0B:");

            $value = trim($valueNode->textContent);

            if ($name !== '') $params[$name] = $value;
        }
    }

    // Изображения из основной галереи
    $images = [];

    $imageNodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' product_gallery ')]" . "//div[contains(concat(' ', normalize-space(@class), ' '), ' product_main_slider ')]" . "//a[@href]");

    foreach ($imageNodes as $node) {
        $imageUrl = trim($node->getAttribute('href'));

        if ($imageUrl !== '') $images[] = $imageUrl;
    }
    return ['title' => $title, 'params' => $params, 'images' => array_values(array_unique($images))];
}

// Обрабатываем товары по очереди
$result = [];
$total = count($products);

foreach ($products as $index => $product) {
    $url = $product['url'] ?? '';

    if ($url === '') continue;

    echo 'Обработка ' . ($index + 1) . '/' . $total . ': ' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "<br>\n";

    $page = getPage($url);

    if (!$page['success']) {
        $product['parse_error'] = $page['error'];
        $result[] = $product;

        echo 'Ошибка: ' . htmlspecialchars($page['error'], ENT_QUOTES, 'UTF-8') . "<br>\n";

        continue;
    }

    $details = parseProduct($page['html']);

    // Сохраняем исходные поля и добавляем подробности
    $product['title'] = $details['title'];
    $product['params'] = $details['params'];
    $product['images'] = $details['images'];

    $result[] = $product;

    echo 'Получено характеристик: ' . count($details['params']) . "<br>\n";

    // Небольшая пауза между запросами
    usleep(300000);
}

// Сохраняем результат в JSON
$outputJson = json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );

if ($outputJson === false)
  die('Ошибка создания JSON: ' . json_last_error_msg());

if (file_put_contents($outputFile, $outputJson . PHP_EOL, LOCK_EX) === false)
  die('Не удалось записать products_details.json');

libxml_clear_errors();

echo '<hr>Готово! Обработано товаров: ' . count($result);
echo '<br>Результат: products_details.json';
