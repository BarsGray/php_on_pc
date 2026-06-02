<?php
$map = [];
$counter = 1;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('upload'));

foreach ($iterator as $file) {

    if (!$file->isFile()) {
      continue;
    }
    // $ext = strtolower($file->getExtension());
    $ext = $file->getExtension();

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'])) {
      continue;
    }

    $newName = $counter . '_' . $file->getFilename();
    copy($file->getPathname(), "temp/$newName");
    $map[$newName] = $file->getPathname();

    $counter++;
}

file_put_contents('map.json',json_encode($map, JSON_PRETTY_PRINT));

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// $map = json_decode(file_get_contents('map.json'),true);

// foreach ($map as $tempFile => $originalPath) {
//     copy("temp/$tempFile",$originalPath);
// }