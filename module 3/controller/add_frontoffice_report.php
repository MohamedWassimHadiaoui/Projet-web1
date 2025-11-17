<?php
session_start();
require_once "reportController.php";

// SERVER-SIDE VALIDATION for Front-Office Form
$errors = [];

// Validate incident type (from radio buttons)
if (empty($_POST['incidentType'])) {
    $errors[] = "Le type d'incident est obligatoire";
}

// Validate role
if (empty($_POST['role'])) {
    $errors[] = "Votre rôle est obligatoire";
}

// Validate title
if (empty($_POST['title'])) {
    $errors[] = "Le titre est obligatoire";
} elseif (strlen(trim($_POST['title'])) < 5) {
    $errors[] = "Le titre doit contenir au moins 5 caractères";
} elseif (strlen(trim($_POST['title'])) > 100) {
    $errors[] = "Le titre ne doit pas dépasser 100 caractères";
}

// Validate description
if (empty($_POST['description'])) {
    $errors[] = "La description est obligatoire";
} elseif (strlen(trim($_POST['description'])) < 10) {
    $errors[] = "La description doit contenir au moins 10 caractères";
}

// Validate incident date (convert DD/MM/YYYY to YYYY-MM-DD for database)
$incident_date = null;
if (!empty($_POST['incidentDate'])) {
    $dateValue = trim($_POST['incidentDate']);
    // Check if format is DD/MM/YYYY
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateValue, $matches)) {
        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];
        $incident_date = "$year-$month-$day";  // Convert to YYYY-MM-DD
        
        // Validate date is not in future
        $date = \DateTime::createFromFormat('Y-m-d', $incident_date);
        if ($date && $date > new \DateTime()) {
            $errors[] = "La date ne peut pas être dans le futur";
        }
    } else {
        $errors[] = "Format de date invalide (utilisez JJ/MM/AAAA)";
    }
}

// If there are errors, send back JSON response
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// SANITIZE inputs
$type = htmlspecialchars(trim($_POST['incidentType']));
$title = htmlspecialchars(trim($_POST['title']));
$description = htmlspecialchars(trim($_POST['description']));
$location = !empty($_POST['location']) ? htmlspecialchars(trim($_POST['location'])) : null;

// Determine priority based on incident type (business logic)
$priority = 'medium';  // Default
if ($type === 'violence') {
    $priority = 'high';
} elseif ($type === 'discrimination') {
    $priority = 'high';
} elseif ($type === 'harassment') {
    $priority = 'high';
}

// CREATE Report object
$r = new Report(
    null,
    $type,
    $title,
    $description,
    $location,
    $incident_date,
    $priority,
    'pending'  // New reports start as pending
);

// SAVE to database
$rc = new reportController();
$success = $rc->addReport($r);

// Return JSON response
header('Content-Type: application/json');
if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Signalement créé avec succès ! Un conseiller vous contactera sous peu.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'errors' => ['Erreur lors de l\'enregistrement. Veuillez réessayer.']
    ]);
}
?>

