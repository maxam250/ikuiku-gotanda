<?php
$inputFile  = __DIR__ . '/items.csv';
$outputFile = __DIR__ . '/items_fixed.csv';

// NOT NULL int columns → default values
$notNullIntDefaults = [
    'shop_id'     => '0',
    'item_active' => '0',
    'item_cate'   => '0',
    'item_new'    => '1',
];

// Columns that exist in the items table
$tableCols = [
    'id', 'item_category_id', 'shop_id', 'item_name', 'item_active',
    'item_cate', 'item_new', 'item_age', 'item_height', 'item_b',
    'item_w', 'item_h', 'item_cup', 'item_shoes', 'item_comment',
    'item_comment2', 'item_blogname', 'item_blog', 'item_url',
    'default_sch_start', 'default_sch_end', 'item_twitter', 'item_instagram',
    'item_tiktok', 'item_youtube', 'item_sort', 'item_line', 'item_tel',
    'email', 'email_verified_at', 'loginid', 'password', 'remember_token',
    'item_p_comment', 'item_p_comment_date', 'created_at', 'updated_at'
];

$in  = fopen($inputFile,  'r');
$out = fopen($outputFile, 'w');

// Read header
$headers = fgetcsv($in);
if ($headers === false) {
    echo "ヘッダーが読めません\n";
    exit(1);
}

// Find extra columns
$extraCols = array_diff($headers, $tableCols);
echo "CSVの列数: " . count($headers) . "\n";
echo "テーブルに存在しない列: " . implode(', ', $extraCols) . "\n";

// Determine which indices to keep
$keepIndices = [];
foreach ($headers as $i => $col) {
    if (in_array($col, $tableCols)) {
        $keepIndices[$i] = $col;
    }
}

// Write header (only table columns)
$newHeaders = array_values($keepIndices);
fputcsv($out, $newHeaders);

$totalRows    = 0;
$fixedNulls   = [];
foreach ($notNullIntDefaults as $col => $def) {
    $fixedNulls[$col] = 0;
}

while (($row = fgetcsv($in)) !== false) {
    $totalRows++;
    $newRow = [];

    foreach ($keepIndices as $i => $col) {
        $val = $row[$i] ?? '';

        // Replace NULL string: use default for NOT NULL int columns, keep NULL marker for nullable
        if ($val === 'NULL') {
            if (isset($notNullIntDefaults[$col])) {
                $val = $notNullIntDefaults[$col];
                $fixedNulls[$col]++;
            }
            // else: keep as 'NULL' string → fputcsv writes it unquoted
        }

        $newRow[] = $val;
    }

    fputcsv($out, $newRow);
}

fclose($in);
fclose($out);

echo "処理完了: {$totalRows} 行\n";
echo "修正したNULL (NOT NULL列):\n";
foreach ($fixedNulls as $col => $count) {
    echo "  {$col}: {$count} 件\n";
}
echo "出力ファイル: {$outputFile}\n";
