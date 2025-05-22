<?php

require __DIR__ . '/../vendor/autoload.php';

$input = file_get_contents("php://input");
file_put_contents(__DIR__ . '/../webhook-log.txt', date('c') . " - " . $input . "\n", FILE_APPEND);

http_response_code(200);
