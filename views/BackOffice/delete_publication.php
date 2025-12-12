<?php
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';

if (!isset($_GET['id'])) {
    header('Location: publications.php');
    exit;
}

$id = intval($_GET['id']);
$controller = new PublicationController();

if ($controller->deletePublication($id)) {
    header('Location: publications.php?success=3');
} else {
    header('Location: publications.php?error=1');
}
exit;
?>

