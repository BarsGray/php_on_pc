<?php
echo 'PHP version: ' . PHP_VERSION . '<br>';
echo 'cURL enabled: ' .
    (function_exists('curl_init') ? 'YES' : 'NO');