<?php
declare(strict_types=1);

require __DIR__ . '/cohere_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    cohere_json_response(['ok' => false, 'error' => 'Méthode non autorisée', 'message' => 'Méthode non autorisée'], 405);
}

$rawInput = file_get_contents('php://input');
$payload = json_decode((string) $rawInput, true);
if (!is_array($payload)) {
    cohere_json_response(['ok' => false, 'error' => 'JSON invalide', 'message' => 'JSON invalide'], 400);
}

$text = cohere_clean_string($payload['text'] ?? '');
if ($text === '') {
    cohere_json_response(['ok' => false, 'error' => 'Texte requis', 'message' => 'Texte requis'], 422);
}

try {
    $config = cohere_config();
} catch (Throwable $e) {
    error_log('[Cohere] Configuration indisponible: ' . $e->getMessage());
    $msg = 'Configuration serveur manquante';
    if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
        // When debugging, return a safe default response rather than an error to allow UI testing
        cohere_json_response(['ok' => true, 'flag' => false, 'scores' => ['hate' => 0.0, 'insult' => 0.0], 'debug_note' => 'Fallback moderation response because COHERE_API_KEY missing'], 200);
    }
    if (function_exists('cohere_debug_enabled') && cohere_debug_enabled()) {
        $msg .= ': ' . $e->getMessage();
    }
    cohere_json_response(['ok' => false, 'error' => $msg, 'message' => $msg], 500);
}
$model = getenv('COHERE_MODERATION_MODEL') ?: ($config['model'] ?? cohere_get_default_model());
$prompt = build_moderation_prompt($text);
$requestBody = [
    'model' => $model,
    'message' => $prompt,
    'temperature' => 0,
    'max_tokens' => 200,
    'stream' => false,
];

$response = cohere_post('/v1/chat', $requestBody);
if (!$response['ok']) {
    $errorMessage = $response['error'] ?? 'Erreur API Cohere';
    cohere_log('error', 'Échec modération Cohere', ['status' => $response['status'], 'error' => $errorMessage]);
    $clientMessage = 'Le service de modération est indisponible.';
    if (cohere_debug_enabled()) {
        $clientMessage .= ' Détails: ' . ($errorMessage ?: '');
    }
    cohere_json_response(['ok' => false, 'error' => $clientMessage, 'message' => $clientMessage], 502);
}

$body = $response['body'];
if (!is_array($body)) {
    cohere_log('error', 'Réponse modération invalide', ['body' => $response['raw']]);
    cohere_json_response(['ok' => false, 'error' => 'Réponse inattendue du service de modération', 'message' => 'Réponse inattendue du service de modération'], 502);
}

$replyText = trim(extract_moderation_reply($body));
if (strpos($replyText, '```') === 0) {
    $replyText = preg_replace('/^```[a-zA-Z0-9_-]*\s*/', '', $replyText) ?? $replyText;
    $replyText = preg_replace('/```$/', '', $replyText) ?? $replyText;
    $replyText = trim($replyText);
}

$parsed = json_decode($replyText, true);
if (!is_array($parsed) || !isset($parsed['flag'])) {
    cohere_log('warning', 'Modération JSON introuvable', ['reply' => $replyText]);
    $parsed = [
        'flag' => false,
        'scores' => ['hate' => 0.0, 'insult' => 0.0],
    ];
}

$scores = [
    'hate' => isset($parsed['scores']['hate']) ? clamp_probability((float) $parsed['scores']['hate']) : 0.0,
    'insult' => isset($parsed['scores']['insult']) ? clamp_probability((float) $parsed['scores']['insult']) : 0.0,
];

$flag = isset($parsed['flag']) ? (bool) $parsed['flag'] : ($scores['hate'] > 0.7 || $scores['insult'] > 0.7);

cohere_json_response([
    'ok' => true,
    'flag' => $flag,
    'scores' => $scores,
]);
function build_moderation_prompt(string $text): string
{
    $template = <<<PROMPT
Tu es un classificateur de sécurité. Analyse le texte entre triples guillemets et retourne UNIQUEMENT un JSON valide avec la structure suivante :
{"flag": bool, "scores": {"hate": float, "insult": float}}
- "flag" = true si le texte contient de la haine, de la menace ou des insultes sévères, sinon false.
- Les scores sont compris entre 0 et 1.
Texte:
"""%s"""
PROMPT;

    $safe = str_replace('"""', '\"\"\"', $text);
    return sprintf($template, $safe);
}

function extract_moderation_reply(array $body): string
{
    if (isset($body['text']) && is_string($body['text'])) {
        return trim($body['text']);
    }

    if (isset($body['message']['content']) && is_array($body['message']['content'])) {
        $chunks = [];
        foreach ($body['message']['content'] as $chunk) {
            if (is_array($chunk) && isset($chunk['text'])) {
                $chunks[] = cohere_clean_string((string) $chunk['text']);
            } elseif (is_string($chunk)) {
                $chunks[] = cohere_clean_string($chunk);
            }
        }
        $joined = trim(implode(' ', array_filter($chunks)));
        if ($joined !== '') {
            return $joined;
        }
    }

    if (isset($body['generations']) && is_array($body['generations'])) {
        foreach ($body['generations'] as $generation) {
            if (isset($generation['text']) && is_string($generation['text'])) {
                $text = trim($generation['text']);
                if ($text !== '') {
                    return $text;
                }
            }
        }
    }

    return '';
}

function clamp_probability(float $value): float
{
    if ($value < 0) {
        return 0.0;
    }
    if ($value > 1) {
        return 1.0;
    }
    return $value;
}
