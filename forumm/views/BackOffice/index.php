<?php
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';
require_once dirname(__DIR__) . '/../controllers/CommentaireController.php';

$publicationController = new PublicationController();
$commentaireController = new CommentaireController();

$publications = $publicationController->listPublications();
$totalPublications = is_array($publications) ? count($publications) : 0;

$pendingCount = $publicationController->countPendingPublications();

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
            
            <?php if ($pendingCount > 0): ?>
            <div class="alert" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <strong style="font-size: 1.25rem;">⚠️ <?php echo $pendingCount; ?> publication(s) en attente de modération</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--color-text-light);">Des utilisateurs ont soumis des publications qui nécessitent votre approbation.</p>
                    </div>
                    <a href="moderation.php" class="btn btn-primary">📋 Modérer maintenant</a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-4" style="margin-bottom: 2rem;">
                <div class="card" style="border-left: 4px solid #f59e0b;">
                    <div class="card-body">
                        <h3 style="color: #f59e0b;"><?php echo $pendingCount; ?></h3>
                        <p>En attente</p>
                        <a href="moderation.php" class="btn btn-primary btn-sm" style="background: #f59e0b; border-color: #f59e0b;">Modérer</a>
                    </div>
                </div>
                <div class="card" style="border-left: 4px solid #10b981;">
                    <div class="card-body">
                        <h3 style="color: #10b981;"><?php echo $totalPublications; ?></h3>
                        <p>Publications approuvées</p>
                        <a href="publications.php" class="btn btn-primary btn-sm">Gérer</a>
                    </div>
                </div>
                <div class="card" style="border-left: 4px solid #3b82f6;">
                    <div class="card-body">
                        <h3 style="color: #3b82f6;"><?php echo $totalCommentaires; ?></h3>
                        <p>Commentaires</p>
                        <a href="commentaires.php" class="btn btn-primary btn-sm">Gérer</a>
                    </div>
                </div>
                <div class="card" style="border-left: 4px solid #8b5cf6;">
                    <div class="card-body">
                        <h3 style="color: #8b5cf6;">Forum</h3>
                        <p>Voir le site</p>
                        <a href="../forum.php" class="btn btn-outline btn-sm" target="_blank">FrontOffice</a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Actions rapides</h2>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="moderation.php" class="btn btn-primary" style="background: #f59e0b; border-color: #f59e0b;">📋 Modération</a>
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

