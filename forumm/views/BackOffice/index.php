<?php
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';
require_once dirname(__DIR__) . '/../controllers/CommentaireController.php';

$publicationController = new PublicationController();
$commentaireController = new CommentaireController();

$publications = $publicationController->listPublications();
$totalPublications = is_array($publications) ? count($publications) : 0;

$totalCommentaires = 0;
if (is_array($publications)) {
    foreach ($publications as $pub) {
        $coms = $commentaireController->listCommentairesByPublication($pub->getIdPublication());
        if (is_array($coms)) {
            $totalCommentaires += count($coms);
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackOffice - Administration</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
    <?php include 'template_header.php'; ?>
    
    <div class="section">
        <div class="container">
            <h1>Tableau de bord - Administration</h1>
            
            <div class="grid grid-3" style="margin-bottom: 2rem;">
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $totalPublications; ?></h3>
                        <p>Publications</p>
                        <a href="publications.php" class="btn btn-primary btn-sm">Gérer</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $totalCommentaires; ?></h3>
                        <p>Commentaires</p>
                        <a href="commentaires.php" class="btn btn-primary btn-sm">Gérer</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3>Forum</h3>
                        <p>Gestion complète</p>
                        <a href="../forum.php" class="btn btn-outline btn-sm" target="_blank">Voir FrontOffice</a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Actions rapides</h2>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="publications.php?action=create" class="btn btn-primary">➕ Nouvelle publication</a>
                        <a href="publications.php" class="btn btn-outline">📋 Liste des publications</a>
                        <a href="commentaires.php" class="btn btn-outline">💬 Liste des commentaires</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'template_footer.php'; ?>
    
    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>

