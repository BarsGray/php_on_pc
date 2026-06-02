<?php
$count = 5;

unlink('f1.txt');

$file = fopen('f1.txt', 'a');
while($count > 0) {
  fwrite($file, 'text' . $count . "\n");
  $count--;
  }
fclose($file);

$file = fopen('f1.txt', 'r');
while(($line = fgets($file)) !== false) {
  echo $line;
}
fclose($file);