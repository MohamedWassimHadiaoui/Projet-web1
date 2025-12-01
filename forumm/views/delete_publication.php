<?php
require_once dirname(__DIR__) . '/controllers/PublicationController.php';

if (!isset($_GET['id'])) {
    header('Location: forum.php');
    exit;
}

$id = intval($_GET['id']);
$controller = new PublicationController();

if ($controller->deletePublication($id)) {
    header('Location: forum.php?success=3');
} else {
    header('Location: forum.php?error=1');
}
exit;
?>

