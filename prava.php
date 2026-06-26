<?php
$path = 'nz';

function chmodRecursive($path, $dirMode = 0755, $fileMode = 0644) {

    if (is_dir($path)) {
        chmod($path, $dirMode);
        // задержка на каждую папку
        // sleep(1); // 1 секунда
        usleep(500000); // 0.2 секунды
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            chmodRecursive($path . '/' . $item, $dirMode, $fileMode);
        }
        echo $path . ' -> DIR chmod<br>';
    } else {
        chmod($path, $fileMode);
        // задержка на каждый файл
        usleep(200000); // 0.2 секунды
        echo $path . ' -> FILE chmod<br>';
    }
}
chmodRecursive($path);