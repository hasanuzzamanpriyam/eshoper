<?php

require 'vendor/autoload.php';

use Rap2hpoutre\FastExcel\FastExcel;

$data = [
    [
        'product_sku' => 12345674558,
        'customer_id' => 11,
        'rating' => 5,
        'comment' => 'Great prod',
        'status' => 1
    ]
];

(new FastExcel($data))->export('test.xlsx');

$collections = (new FastExcel)->import('test.xlsx');

foreach ($collections as $collection) {
    echo "product_sku value: " . $collection['product_sku'] . " (type: " . gettype($collection['product_sku']) . ")\n";
    echo "string cast: " . (string)$collection['product_sku'] . "\n";
}
