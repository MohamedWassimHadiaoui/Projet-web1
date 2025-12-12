<?php
require_once dirname(__DIR__) . '/../controllers/CommentaireController.php';

if (!isset($_GET['id'])) {
    header('Location: commentaires.php');
    exit;
}

$id = intval($_GET['id']);
$controller = new CommentaireController();

if ($controller->deleteCommentaire($id)) {
    header('Location: commentaires.php?success=3');
} else {
    header('Location: commentaires.php?error=1');
}
exit;
?>

