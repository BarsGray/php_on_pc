<?php
$map = [];
$counter = 1;

if (!is_dir('temp')) {
  mkdir('temp');
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('img'));
  
foreach ($iterator as $file) {

    if (!$file->isFile()) {
      continue;
    }
    // $ext = strtolower($file->getExtension());
    $ext = $file->getExtension();

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'JPG', 'JPEG', 'PNG', 'WEBP', 'SVG'])) {
      continue;
    }
    
    // Пример
    // if (!is_dir("temp/" . str_replace($file->getFilename(), '', $file->getPathname()))) {
    //   mkdir("temp/" . str_replace($file->getFilename(), '', $file->getPathname()), true);
    // }
    // if (!is_dir("temp/" . dirname($file->getPathname()))) {
    //   mkdir("temp/" . dirname($file->getPathname()), true);
    // }

    $newName = $counter . '_' . $file->getFilename();
    
    // Пример
    // copy($file->getPathname(), "temp/" . $file->getPathname());
    
    copy($file->getPathname(), "temp/" . $newName);
    $map[$newName] = $file->getPathname();

    // Пример
    // file_put_contents('test.php', $file . "\n", FILE_APPEND);
    // print_r(get_class_methods($file));

    $counter++;
}

file_put_contents('map.json',json_encode($map, JSON_PRETTY_PRINT));

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// $map = json_decode(file_get_contents('map.json'),true);

// foreach ($map as $tempFile => $originalPath) {
//     copy("temp/$tempFile",$originalPath);
// }