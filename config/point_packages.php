<?php

/**
 * ポイント購入パッケージ設定
 *
 * id     : パッケージ識別子（URLパラメータとして使用）
 * price  : 決済金額（円）
 * points : 付与ポイント数
 * label  : 表示用ラベル
 */
return [
    'packages' => [
        [
            'id'     => 1,
            'price'  => 500,
            'points' => 500,
            'label'  => '500円 → 500pt',
        ],
        [
            'id'     => 2,
            'price'  => 1000,
            'points' => 1200,
            'label'  => '1,000円 → 1,200pt（お得）',
        ],
        [
            'id'     => 3,
            'price'  => 3000,
            'points' => 4000,
            'label'  => '3,000円 → 4,000pt（大変お得）',
        ],
    ],
];
