<?php
declare(strict_types=1);

require __DIR__ . '/cohere_config.php';

try {
    $cfg = cohere_config();
} catch (Throwable $e) {
    cohere_json_response(['ok' => false, 'configured' => false, 'message' => $e->getMessage()], 503);
}

$status = [
    'ok' => true,
    'configured' => true,
    'model' => $cfg['model'] ?? cohere_get_default_model(),
    'debug' => !empty($cfg['debug']),
    'using_inline_key' => function_exists('cohere_using_inline_key') ? cohere_using_inline_key() : false,
];
cohere_json_response($status);