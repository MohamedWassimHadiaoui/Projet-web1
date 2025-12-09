<?php
declare(strict_types=1);

require __DIR__ . '/cohere_config.php';

// Quick test endpoint: if called with ?test=1 (GET or POST) we return a sample Chat response
if (isset($_GET['test']) && $_GET['test'] == '1') {
    $sample = [
        'id' => 'test-123',
        'object' => 'chat.completion',
        'created' => time(),
        'model' => cohere_get_default_model(),
        'ok' => true,
        'reply' => 'Réponse de test — assistant optique : voici un conseil de test pour les verres progressifs.',
        'text' => 'Réponse de test — assistant optique : voici un conseil de test pour les verres progressifs.',
        'message' => [ 'content' => [ [ 'type' => 'text', 'text' => 'Réponse de test — assistant optique : voici un conseil de test pour les verres progressifs.' ] ] ],
        'choices' => [
            [
                'index' => 0,
                'message' => [ 'role' => 'assistant', 'content' => [ [ 'type' => 'text', 'text' => 'Réponse de test — assistant optique : voici un conseil de test pour les verres progressifs.' ] ] ],
                'finish_reason' => 'stop',
            ]
        ]
    ];
    cohere_json_response($sample);
}
// If `call_cohere=1` is provided and COHERE_DEBUG is enabled, attempt to make one request to Cohere
if (isset($_GET['call_cohere']) && $_GET['call_cohere'] == '1') {
    try {
        $config = cohere_config();
    } catch (Throwable $e) {
        error_log('[Cohere] Configuration indisponible: ' . $e->getMessage());
        // If debug is enabled, fall back to a test config so we can return a sample reply
        $debug = (bool) filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        if ($debug) {
            $config = [
                'api_key' => '',
                'base_url' => rtrim(COHERE_API_BASE, '/'),
                'model' => cohere_get_default_model(),
                'debug' => true,
                'timeout' => COHERE_DEFAULT_TIMEOUT,
                'log_file' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'cohere.log',
            ];
        } else {
            $msg = 'Configuration serveur manquante';
            if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
                $msg .= ': ' . $e->getMessage();
            }
            cohere_json_response(['ok' => false, 'error' => $msg, 'message' => $msg], 500);
        }
    }
    $testMessages = [
        [ 'role' => 'system', 'content' => [ [ 'type' => 'text', 'text' => cohere_get_system_prompt() ] ] ],
        [ 'role' => 'user', 'content' => [ [ 'type' => 'text', 'text' => 'Test de connectivité Cohere depuis le serveur.' ] ] ],
    ];

    $payloadTest = cohere_build_chat_request(
        $testMessages,
        'Test de connectivité Cohere depuis le serveur.',
        cohere_get_system_prompt(),
        $config['model'] ?? cohere_get_default_model(),
        ['max_tokens' => 50]
    );
    $resp = cohere_post('/v1/chat', $payloadTest);
    cohere_json_response(['ok' => $resp['ok'], 'status' => $resp['status'], 'error' => $resp['error'], 'body' => $resp['body'], 'raw' => $resp['raw']]);
}
// Capture request payload early so test endpoints can echo it safely.
$rawInput = '';
$payload = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = (string) file_get_contents('php://input');
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

// If `testPost=1` is provided (POST) return a safe echo of the POST payload so clients can test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['testPost']) && $_GET['testPost'] == '1') {
    $echoPayload = is_array($payload) ? $payload : [];
    $echoMessage = isset($echoPayload['message']) ? (string) $echoPayload['message'] : '';
    $replyPreview = $echoMessage !== '' ? $echoMessage : json_encode($echoPayload['messages'] ?? []);

    $echo = [
        'ok' => true,
        'reply' => 'Echo: ' . substr((string) $replyPreview, 0, 300),
        'text' => $echoMessage,
        'message' => ['content' => [ [ 'type' => 'text', 'text' => $echoMessage ] ] ],
        'echo_payload' => $echoPayload,
    ];
    if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
        $loggedPayload = is_array($payload) ? $payload : ['raw' => substr($rawInput, 0, 2000)];
        cohere_log('debug', 'TESTPOST_ECHO', ['payload' => substr(json_encode($loggedPayload), 0, 2000)]);
    }
    cohere_json_response($echo);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    cohere_json_response(['ok' => false, 'error' => 'Méthode non autorisée', 'message' => 'Méthode non autorisée'], 405);
}

if (!is_array($payload)) {
    cohere_json_response(['ok' => false, 'error' => 'JSON invalide', 'message' => 'JSON invalide'], 400);
}

// Log the incoming payload in debug mode to help identify parsing / shape issues
if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
    cohere_log('debug', 'INCOMING_REQUEST', ['method' => $_SERVER['REQUEST_METHOD'], 'payload' => substr(json_encode($payload), 0, 2000)]);
}

// Allow either a single 'message' string OR a 'messages' array (chat-style)
$message = cohere_clean_string($payload['message'] ?? '');
$hasMessagesArray = isset($payload['messages']) && is_array($payload['messages']) && count($payload['messages']) > 0;
if (!$hasMessagesArray) {
    if ($message === '' || mb_strlen($message) < 3) {
        cohere_json_response(['ok' => false, 'error' => 'Message trop court', 'message' => 'Message trop court'], 422);
    }
}

$context = [];
if (isset($payload['context']) && is_array($payload['context'])) {
    $context = cohere_sanitize_context($payload['context']);
}

try {
    $config = cohere_config();
} catch (Throwable $e) {
    error_log('[Cohere] Configuration indisponible: ' . $e->getMessage());
    $debug = (bool) filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN);
    if ($debug) {
        $config = [
            'api_key' => '',
            'base_url' => rtrim(COHERE_API_BASE, '/'),
            'model' => cohere_get_default_model(),
            'debug' => true,
            'timeout' => COHERE_DEFAULT_TIMEOUT,
            'log_file' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'cohere.log',
        ];
    } else {
        $msg = 'Configuration serveur manquante';
        if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
            $msg .= ': ' . $e->getMessage();
        }
        cohere_json_response(['ok' => false, 'error' => $msg, 'message' => $msg], 500);
    }
}
$systemPrompt = cohere_get_system_prompt();

$contextText = empty($context) ? 'Aucun contexte fourni.' : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$messageBlock = $systemPrompt . "\n\nContexte utilisateur:\n" . $contextText . "\n\nDemande utilisateur:\n" . $message;

// Pick model optionally provided by client or fallback to default
$requestModel = cohere_clean_string($payload['model'] ?? cohere_get_default_model());

// Build or accept a chat-style message array compatible with Cohere Chat API
$messages = [];
if (isset($payload['messages']) && is_array($payload['messages'])) {
    // Use messages provided by client (sanitizing strings)
    foreach ($payload['messages'] as $m) {
        if (!is_array($m)) continue;
        $role = isset($m['role']) ? cohere_clean_string((string)$m['role']) : 'user';
        $contentChunks = [];
        if (isset($m['content']) && is_array($m['content'])) {
            foreach ($m['content'] as $chunk) {
                if (is_array($chunk) && isset($chunk['text'])) {
                    $contentChunks[] = [ 'type' => 'text', 'text' => cohere_clean_string((string)$chunk['text']) ];
                } elseif (is_string($chunk)) {
                    $contentChunks[] = [ 'type' => 'text', 'text' => cohere_clean_string($chunk) ];
                }
            }
        }
        if (!empty($contentChunks)) {
            $messages[] = [ 'role' => $role, 'content' => $contentChunks ];
        }
    }
}

if (empty($messages)) {
    // fallback: construct messages from our system prompt and user message
    $messages = [
        [ 'role' => 'system', 'content' => [ [ 'type' => 'text', 'text' => $systemPrompt ] ] ],
    ];
    if (!empty($context)) {
        $messages[] = [ 'role' => 'system', 'content' => [ [ 'type' => 'text', 'text' => 'Contexte: ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ] ] ];
    }
    $messages[] = [ 'role' => 'user', 'content' => [ [ 'type' => 'text', 'text' => $message ] ] ];
}

// If we have context, append it as a system message (safe, non-user content)
if (!empty($context)) {
    $contextText = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $messages[] = [
        'role' => 'system',
        'content' => [ [ 'type' => 'text', 'text' => 'Contexte: ' . $contextText ] ]
    ];
}

// The user's message is included in the constructed `$messages` above for the fallback case.

$requestBody = cohere_build_chat_request(
    $messages,
    $message,
    $systemPrompt,
    $requestModel ?: $config['model']
);

$response = null;
if (empty($config['api_key']) && (bool) filter_var(getenv('COHERE_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    // Build a fake response when COHERE_DEBUG is enabled and no API key is present.
    $fakeText = 'Réponse de test (fallback debug) : ' . ($message ?: 'Aucun message fourni.');
    $response = [
        'ok' => true,
        'status' => 200,
        'error' => null,
        'body' => [
            'id' => 'debug-fake',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => $requestBody['model'],
            'message' => ['content' => [ [ 'type' => 'text', 'text' => $fakeText ] ]],
            'text' => $fakeText,
            'choices' => [ [ 'index' => 0, 'message' => ['role' => 'assistant', 'content' => [['type' => 'text', 'text' => $fakeText]] ], 'finish_reason' => 'stop'] ],
        ],
        'raw' => json_encode(['fake' => true, 'text' => $fakeText]),
    ];
} else {
    $response = cohere_post('/v1/chat', $requestBody);
}
if (!$response['ok']) {
    $errorMessage = $response['error'] ?? 'Erreur API Cohere';
    $details = $response['body']['message'] ?? $response['raw'] ?? null;
    cohere_log('error', 'Échec appel Cohere chat', ['status' => $response['status'], 'error' => $errorMessage, 'details' => $details]);
    $clientMessage = 'Impossible de contacter le chatbot pour le moment.';
    if (cohere_debug_enabled()) {
        $clientMessage .= ' Détails: ' . ($errorMessage ?: '');
    }
    cohere_json_response(['ok' => false, 'error' => $clientMessage, 'message' => $clientMessage], 502);
}

// Try extract reply; if empty/null, attempt to build a fallback using content chunks and log details.
$reply = cohere_extract_chat_reply($response['body']);
if ($reply === null || $reply === '') {
    cohere_log('warning', 'Réponse Cohere vide ou illisible', ['body' => $response['body']]);
    // Attempt a last-ditch try by inspecting message.content if present
    if (isset($response['body']['message']['content']) && is_array($response['body']['message']['content'])) {
        $chunks = [];
        foreach ($response['body']['message']['content'] as $c) {
            if (is_array($c) && isset($c['text'])) {
                $chunks[] = cohere_clean_string((string) $c['text']);
            } elseif (is_string($c)) {
                $chunks[] = cohere_clean_string($c);
            }
        }
        $join = trim(implode(' ', array_filter($chunks)));
        if ($join !== '') {
            $reply = $join;
        }
    }

    if ($reply === null || $reply === '') {
        // Still no reply: return a helpful message to the client
        cohere_json_response(['ok' => false, 'error' => 'Le chatbot n\'a pas renvoyé de texte.', 'message' => 'Le chatbot n\'a pas renvoyé de texte.'], 502);
    }
}

// If debug enabled, log the raw cohere body and our outgoing JSON so it's easier to debug parse issues
if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
    cohere_log('debug', 'Cohere raw response (snippet)', ['raw' => substr(json_encode($response['body']), 0, 1024)]);

    // Log raw response into a separate 'COHERE_RAW' record
    if (function_exists('cohere_log_raw_response')) {
        cohere_log_raw_response($response);
    }
}

// Build a response compatible with typical chat completion clients (including C++ samples)
// Also add top-level 'id/object/created' for Chat Completion compatibility
// Build backwards-compatible "chat completion" shaped response; include message and text
$messageWrapper = $response['body']['message'] ?? null;
if ($messageWrapper === null) {
    $messageWrapper = [
        'content' => [ [ 'type' => 'text', 'text' => $reply ] ]
    ];
}
$result = [
    'message' => $messageWrapper,
    'text' => $reply,
    'id' => $response['body']['id'] ?? null,
    'object' => $response['body']['object'] ?? null,
    'created' => $response['body']['created'] ?? null,
    'ok' => true,
    'reply' => $reply,
    'model' => $requestBody['model'] ?? $config['model'],
    'cohere' => [
        'id' => $response['body']['id'] ?? null,
        'object' => $response['body']['object'] ?? null,
        'created' => $response['body']['created'] ?? null,
        'model' => $response['body']['model'] ?? ($requestBody['model'] ?? $config['model']),
        // keep the original body for debugging, but avoid printing the API key
        'body' => $response['body'] ?? null,
    ],
    'choices' => $response['body']['choices'] ?? [
        [
            'index' => 0,
            'message' => [
                'role' => 'assistant',
                'content' => [
                    [ 'type' => 'text', 'text' => $reply ]
                ]
            ],
            'finish_reason' => 'stop'
        ]
    ]
];

// Log outgoing result if debugging is enabled so we can check what the client receives
if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
    cohere_log('debug', 'OUTGOING_RESPONSE', ['result' => substr(json_encode($result), 0, 2000)]);
}

cohere_json_response($result);

/**
 * Sanitizes nested context data.
 */
function cohere_sanitize_context(array $context): array
{
    $clean = [];
    foreach ($context as $key => $value) {
        $safeKey = cohere_clean_string(is_string($key) ? $key : (string) $key);
        if (is_array($value)) {
            $clean[$safeKey] = cohere_sanitize_context($value);
        } elseif (is_scalar($value)) {
            $clean[$safeKey] = cohere_clean_string((string) $value);
        }
    }
    return $clean;
}

/**
 * Flattens a Cohere-style content array into plain text.
 */
function cohere_flatten_content_chunks($chunks): string
{
    if (!is_array($chunks)) {
        return is_string($chunks) ? cohere_clean_string($chunks) : '';
    }

    $texts = [];
    foreach ($chunks as $chunk) {
        if (is_array($chunk) && isset($chunk['text'])) {
            $texts[] = cohere_clean_string((string) $chunk['text']);
        } elseif (is_string($chunk)) {
            $texts[] = cohere_clean_string($chunk);
        }
    }

    return trim(implode(' ', array_filter($texts)));
}

/**
 * Builds a Cohere /v1/chat payload from OpenAI-style messages.
 */
function cohere_build_chat_request(array $messages, string $fallbackUserMessage, string $systemPrompt, string $model, array $options = []): array
{
    $temperature = isset($options['temperature']) ? (float) $options['temperature'] : 0.3;
    $maxTokens = isset($options['max_tokens']) ? (int) $options['max_tokens'] : 400;
    $stream = isset($options['stream']) ? (bool) $options['stream'] : false;

    $chatHistory = [];
    $preambleChunks = [];
    $lastUserMessage = $fallbackUserMessage;
    $lastUserIndex = null;

    if (!empty($messages)) {
        for ($idx = count($messages) - 1; $idx >= 0; $idx--) {
            $entry = $messages[$idx];
            if (!is_array($entry)) {
                continue;
            }
            $role = strtolower($entry['role'] ?? '');
            if ($role !== 'user') {
                continue;
            }
            $text = cohere_flatten_content_chunks($entry['content'] ?? []);
            if ($text === '') {
                continue;
            }
            $lastUserMessage = $text;
            $lastUserIndex = $idx;
            break;
        }
    }

    foreach ($messages as $idx => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $role = strtolower($entry['role'] ?? '');
        $text = cohere_flatten_content_chunks($entry['content'] ?? []);
        if ($text === '') {
            continue;
        }

        if ($role === 'system') {
            $preambleChunks[] = $text;
            continue;
        }

        if ($role === 'user') {
            if ($idx === $lastUserIndex) {
                continue;
            }
            $chatHistory[] = ['role' => 'USER', 'message' => $text];
            continue;
        }

        $chatHistory[] = ['role' => 'CHATBOT', 'message' => $text];
    }

    if ($systemPrompt !== '') {
        array_unshift($preambleChunks, $systemPrompt);
    }

    $preambleChunks = array_values(array_filter(array_map('trim', $preambleChunks)));
    $preambleChunks = array_values(array_unique($preambleChunks));
    $preamble = trim(implode("\n\n", $preambleChunks));

    $body = [
        'model' => $model,
        'message' => $lastUserMessage,
        'temperature' => $temperature,
        'max_tokens' => $maxTokens,
        'stream' => $stream,
    ];

    if (!empty($chatHistory)) {
        $body['chat_history'] = $chatHistory;
    }
    if ($preamble !== '') {
        $body['preamble'] = $preamble;
    }

    return $body;
}

/**
 * Extracts the assistant reply from Cohere chat payload.
 */
function cohere_extract_chat_reply(?array $body): ?string
{
    if (!$body) {
        return null;
    }

    if (isset($body['message']['content']) && is_array($body['message']['content'])) {
        $texts = [];
        foreach ($body['message']['content'] as $chunk) {
            if (is_array($chunk)) {
                // Support both 'text' and 'type'/'text' combos
                if (isset($chunk['text']) && is_string($chunk['text'])) {
                    $texts[] = cohere_clean_string((string) $chunk['text']);
                } elseif (isset($chunk['type']) && isset($chunk['text'])) {
                    $texts[] = cohere_clean_string((string) $chunk['text']);
                }
            } elseif (is_string($chunk)) {
                $texts[] = cohere_clean_string($chunk);
            }
        }
        $joined = trim(implode(" ", array_filter($texts)));
        if ($joined !== '') {
            return $joined;
        }
    }

    if (isset($body['text']) && is_string($body['text'])) {
        $text = cohere_clean_string($body['text']);
        if ($text !== '') {
            return $text;
        }
    }

    if (isset($body['generations']) && is_array($body['generations'])) {
        $texts = [];
        foreach ($body['generations'] as $generation) {
            if (isset($generation['text']) && is_string($generation['text'])) {
                $texts[] = cohere_clean_string((string) $generation['text']);
            }
        }
        $joined = trim(implode(" ", array_filter($texts)));
        if ($joined !== '') {
            return $joined;
        }
    }

    return null;
}
