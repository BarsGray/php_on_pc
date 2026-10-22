<?php
$zip = new ZipArchive();

$zip_name = 'wordpress-7.0.zip';

if ($zip->open(__DIR__ . "/$zip_name") === TRUE) {
  $zip->extractTo(__DIR__);
  $zip->close();
  echo __DIR__ . "/$zip_name<br>";
  echo 'Архив распакован';
} else {
  echo __DIR__ . "/$zip_name<br>";
  echo 'Не удалось открыть архив';
}