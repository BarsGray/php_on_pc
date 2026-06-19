<?php
$zip = new ZipArchive();

if ($zip->open(__DIR__ . '/wordpress-7.0.zip') === TRUE) {
  $zip->extractTo(__DIR__);
  $zip->close();
  echo __DIR__ . '/wordpress-7.0.zip';
  echo 'Архив распакован';
} else {
  echo 'Не удалось открыть архив';
}