<?php
$inputFile  = __DIR__ . '/ranks.csv';
$outputFile = __DIR__ . '/ranks_fixed.csv';

$in  = fopen($inputFile,  'r');
$out = fopen($outputFile, 'w');

$headers = fgetcsv($in);
fputcsv($out, $headers);

$rows = 0;
while (($row = fgetcsv($in)) !== false) {
    $rows++;
    $fixed = array_map(fn($v) => $v === 'NULL' ? '' : $v, $row);
    fputcsv($out, $fixed);
}

fclose($in);
fclose($out);
echo "完了: {$rows}行 → items_fixed.csv\n";
