<?php
  libxml_use_internal_errors(true);

  $url = 'https://delica.testvzh.ru/catalog/bytovaya-produktsiya/';
  $catalog = getPage($url);
  $dom = new DOMDocument();
  $dom->loadHTML('<?xml encoding="UTF-8"?>' . $catalog);

  $xpath = new DOMXPath($dom);
  $items = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' catalog_item ')]");

  $result = [];
  $links = [];
  
  foreach($items as $item) {
    $linkNode = $xpath->query(".//a[@href]", $item)->item(0);
    $links[] = $linkNode->getAttribute('href');
  }

  foreach($links as $item) {
    $page = getPage($item);
    $details = parseProduct($page);

    $result['title'] = $details['title'];
    $result['params'] = $details['params'];
    $result['images'] = $details['images'];

    usleep(300000);
  }

  $outputJson = json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );

  echo '<pre>';
  print_r($outputJson);
  echo '</pre>';

  libxml_clear_errors();

  function getPage($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>30]);
    
    $html = curl_exec($ch);
    $error = curl_error($ch);
    $status  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // if ($html === false || $status < 200 || $status >= 300)
    //   return ['success' => false,'error' => $error ?: "HTTP $status"];
    // return ['success' => true,'html' => $html];

    return $html;
  } 

  function parseProduct($html) {
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