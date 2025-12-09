<?php
declare(strict_types=1);

/**
 * Shared Cohere helper utilities.
 */

const COHERE_API_BASE = 'https://api.cohere.ai';
const COHERE_API_VERSION = '2022-12-06';
// Match the user's C++ example default model
const COHERE_DEFAULT_MODEL = 'command-a-03-2025';
const COHERE_DEFAULT_TIMEOUT = 30;

// OPTIONAL: Inline API key for local development only. DO NOT COMMIT this into version control.
// If you want to set the API key directly in code for local testing only, put it here.
// Example: const COHERE_INLINE_API_KEY = 'sk-xxxxx';
// Leave empty in production or before committing to your repo.
const COHERE_INLINE_API_KEY = 'Lq5tWwCzYa5WsQsKIVJIJgpfq7U13MjkNJ8sx1Ji';

/**
 * Returns the resolved configuration (API key, logging, etc.).
 */
function cohere_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    // Load simple dotenv file (if present) for non-key settings, but prefer inline constant before env/file.
    cohere_load_dotenv();

    $apiKey = null;
    $usingInlineKey = false;

    if (defined('COHERE_INLINE_API_KEY')) {
        $inline = trim((string) COHERE_INLINE_API_KEY);
        if ($inline !== '') {
            $apiKey = $inline;
            $usingInlineKey = true;
        }
    }

    if ($apiKey === null || $apiKey === '') {
        $envKey = trim((string) getenv('COHERE_API_KEY'));
        if ($envKey !== '') {
            $apiKey = $envKey;
        }
    }

    if ($apiKey === null || $apiKey === '') {
        $apiKey = cohere_read_key_file();
    }

    if ($apiKey === '' || $apiKey === null) {
        throw new RuntimeException('Clé API Cohere manquante. Définissez COHERE_API_KEY en var d\'env ou créez `config/cohere.key`.');
    }

    $root = dirname(__DIR__);
    $logDir = $root . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true);
    }

    $logFile = $logDir . DIRECTORY_SEPARATOR . 'cohere.log';
    cohere_rotate_log($logFile);

    $config = [
        'api_key' => $apiKey,
        'base_url' => rtrim(COHERE_API_BASE, '/'),
        'model' => trim((string) getenv('COHERE_MODEL')) ?: COHERE_DEFAULT_MODEL,
        'debug' => filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN),
        'timeout' => (int) (getenv('COHERE_TIMEOUT') ?: COHERE_DEFAULT_TIMEOUT),
        'log_file' => $logFile,
        'using_inline_key' => $usingInlineKey,
    ];

    return $config;
}

/**
 * Reads key from config/cohere.key if present.
 */
function cohere_read_key_file(): ?string
{
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cohere.key';
    if (!is_readable($path)) {
        return null;
    }

    $contents = trim((string) file_get_contents($path));
    return $contents !== '' ? $contents : null;
}

/**
 * Lightweight log rotation (1MB threshold).
 */
function cohere_rotate_log(string $file): void
{
    $maxSize = 1024 * 1024; // 1MB
    if (file_exists($file) && filesize($file) >= $maxSize) {
        $timestamp = date('YmdHis');
        @rename($file, $file . '.' . $timestamp);
    }
}

/**
 * Appends an entry to the log file.
 */
function cohere_log(string $level, string $message, array $context = []): void
{
    $config = cohere_config();
    $line = sprintf('[%s] %s: %s', date('c'), strtoupper($level), $message);
    if (!empty($context)) {
        $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    $line .= PHP_EOL;
    file_put_contents($config['log_file'], $line, FILE_APPEND);
}

/**
 * Sends a JSON response and terminates the script.
 */
function cohere_json_response(array $payload, int $status = 200): void
{
    // Ensure no stray output is sent; clear any output buffering and force valid JSON
    // Clean any output buffers to ensure only JSON is sent (avoid PHP notices, HTML, BOM, etc.)
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: ' . strlen((string)$json));
    echo $json;
    // Flush and end script
    @flush();
    // If debug env enabled, append outgoing JSON snippet to the debug log for diagnostics
    if (filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
        $root = dirname(__DIR__);
        $logDir = $root . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }
        $logFile = $logDir . DIRECTORY_SEPARATOR . 'cohere.log';
        $line = sprintf("[%s] DEBUG_OUT: %s\n", date('c'), substr($json, 0, 2000));
        @file_put_contents($logFile, $line, FILE_APPEND);
    }
    exit;
}

/**
 * Performs a POST request against Cohere.
 */
function cohere_post(string $endpoint, array $body): array
{
    $config = cohere_config();
    $url = $config['base_url'] . $endpoint;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => max(1, $config['timeout']),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $config['api_key'],
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);

    // If debug enabled, log outgoing request body (without API key)
    if (filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
        $debugBody = $body;
        // avoid logging any API keys (none in body normally) but keep model and messages
        $safe = json_encode($debugBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        cohere_log('debug', 'Cohere request: ' . substr($safe, 0, 2000));
    }
    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $decoded = null;
    $jsonError = null;
    if ($response !== false && $response !== null) {
        $decoded = json_decode((string) $response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $jsonError = json_last_error_msg();
            $decoded = null;
        }
    }

    $ok = $errno === 0 && $status < 400 && $jsonError === null;

    if (filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
        $snippet = is_string($response) ? substr(preg_replace('/\s+/', ' ', $response), 0, 2000) : '';
        cohere_log('debug', 'COHERE_HTTP_RESPONSE', [
            'endpoint' => $endpoint,
            'status' => $status ?: 0,
            'errno' => $errno,
            'error' => $errno !== 0 ? $error : $jsonError,
            'raw' => $snippet,
        ]);
    }

    return [
        'ok' => $ok,
        'status' => $status ?: 0,
        'error' => $errno !== 0 ? $error : $jsonError,
        'body' => $decoded,
        'raw' => $response,
    ];
}

// Add debug logging wrapper that writes larger chunks for full response content when COHERE_DEBUG.
function cohere_log_raw_response(array $response): void
{
    if (!filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN)) return;
    $root = dirname(__DIR__);
    $logDir = $root . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    $logFile = $logDir . DIRECTORY_SEPARATOR . 'cohere.log';
    $snippet = isset($response['raw']) ? substr((string)$response['raw'], 0, 2000) : '';
    $line = sprintf('[%s] COHERE_RAW: status=%s error=%s snippet=%s\n', date('c'), $response['status'], str_replace("\n", ' ', (string)$response['error']), substr(preg_replace('/\s+/', ' ', $snippet), 0, 2000));
    @file_put_contents($logFile, $line, FILE_APPEND);
}


/**
 * Helper to sanitize untrusted values.
 */
function cohere_clean_string(?string $value): string
{
    $value = $value ?? '';
    $value = strip_tags($value);
    return trim($value);
}

/**
 * Indicates whether verbose debug output is enabled.
 */
function cohere_debug_enabled(): bool
{
    $config = cohere_config();
    return !empty($config['debug']);
}

/**
 * Indicates whether an inline API key is set (dev-only).
 */
function cohere_using_inline_key(): bool
{
    try {
        $config = cohere_config();
        return !empty($config['using_inline_key']);
    } catch (Throwable $e) {
        return defined('COHERE_INLINE_API_KEY') && trim((string) COHERE_INLINE_API_KEY) !== '';
    }
}

/**
 * Load a simple .env file from config/.env into environment variables.
 * This is intentionally minimal and only supports plain KEY=VALUE lines, no expansion.
 */
function cohere_load_dotenv(): void
{
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . '.env';
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        $idx = strpos($line, '=');
        if ($idx === false) {
            continue;
        }

        $key = trim(substr($line, 0, $idx));
        $val = trim(substr($line, $idx + 1));
        if ($key === '') {
            continue;
        }

        $firstChar = $val[0] ?? '';
        $lastChar = $val !== '' ? substr($val, -1) : '';
        if ($val !== '' && $firstChar === '"' && $lastChar === '"') {
            $val = substr($val, 1, -1);
        } elseif ($val !== '' && $firstChar === "'" && $lastChar === "'") {
            $val = substr($val, 1, -1);
        }

        if (getenv($key) !== false) {
            continue;
        }

        putenv(sprintf('%s=%s', $key, $val));
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

/**
 * Returns the system prompt to use. Can be set via COHERE_SYSTEM_PROMPT env var.
 * Defaults to 'optique' to match the user's sample. If you need the older mediation
 * behavior, set COHERE_SYSTEM_PROMPT to the desired prompt string.
 */
function cohere_get_system_prompt(): string
{
    $val = getenv('COHERE_SYSTEM_PROMPT') ?: '';
    if ($val !== '') return (string) $val;
    // Default 'optique' style system prompt, similar to the C++ sample.
    return "SYSTEM: Tu es un assistant optique, tu dois répondre en français, être concis, utile, et donner des conseils pratiques lorsque la demande concerne l'organisation d'événements ou la médiation locale. Si la demande est hors-sujet, répondre poliment en indiquant tes limites.";
}

/**
 * Returns the default model name (can be overridden with COHERE_MODEL env var).
 */
function cohere_get_default_model(): string
{
    $configModel = trim((string) getenv('COHERE_MODEL'));
    if ($configModel !== '') return $configModel;
    return COHERE_DEFAULT_MODEL;
}