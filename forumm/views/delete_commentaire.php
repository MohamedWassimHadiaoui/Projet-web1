<?php
require_once dirname(__DIR__) . '/controllers/CommentaireController.php';

if (!isset($_GET['id']) || !isset($_GET['id_publication'])) {
    header('Location: forum.php');
    exit;
}

$id = intval($_GET['id']);
$id_publication = intval($_GET['id_publication']);
$controller = new CommentaireController();

if ($controller->deleteCommentaire($id)) {
    header('Location: view_publication.php?id=' . $id_publication . '&success=2');
} else {
    header('Location: view_publication.php?id=' . $id_publication . '&error=1');
}
exit;
?>

