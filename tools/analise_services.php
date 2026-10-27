<?php
$pub = file_get_contents('app/Config/Services.php');
$adm = file_get_contents('adm/app/Config/Services.php');
preg_match_all('/public static function (\w+)/', $pub, $mpub);
preg_match_all('/public static function (\w+)/', $adm, $madm);
$common = array_intersect($mpub[1], $madm[1]);
echo 'Métodos comuns: ' . count($common) . PHP_EOL;
foreach ($common as $m) echo '- ' . $m . PHP_EOL;
echo PHP_EOL . 'Admin exclusivos: ' . count(array_diff($madm[1], $mpub[1])) . PHP_EOL;
