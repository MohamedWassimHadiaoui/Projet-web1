<?php
/**
 * index.php - Front Controller Simple
 * Redirige vers les fichiers PHP/HTML directement
 */

$p = isset($_GET['p']) ? trim($_GET['p'], '/ ') : 'index';

// Fichiers autorisés
$allowedPages = [
    'index' => 'index.html',
    'help-request' => 'help-request.php',
    'forum' => 'forum.html',
    'events' => 'events.html',
    'login' => 'login.html',
    'register' => 'register.html',
    'profile' => 'profile.html',
    'reports-management' => 'reports-management.html',
    'create-report' => 'create-report.html',
    'dashboard' => 'dashboard.php',
    'admin' => 'dashboard.php',
    'admin/dashboard' => 'dashboard.php',
];

// Récupérer le fichier correspondant
$file = $allowedPages[$p] ?? 'index.html';
$filePath = __DIR__ . '/' . $file;

if (file_exists($filePath)) {
    // Pour les fichiers PHP, les inclure; pour HTML, rediriger ou inclure
    if (substr($file, -4) === '.php') {
        include $filePath;
    } else {
        include $filePath;
    }
    exit;
}

// Page non trouvée
http_response_code(404);
echo "Page non trouvée: " . htmlspecialchars($p);
