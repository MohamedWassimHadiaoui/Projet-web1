<?php
require_once dirname(__DIR__) . '/controllers/PublicationController.php';
require_once dirname(__DIR__) . '/models/Publication.php';

$controller = new PublicationController();
$error = '';

if (!isset($_GET['id'])) {
    header('Location: forum.php');
    exit;
}

$id = intval($_GET['id']);
$publication = $controller->getPublicationById($id);

if (!$publication) {
    header('Location: forum.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    
    if (empty($titre) || strlen($titre) < 5) {
        $error = 'Le titre doit contenir au moins 5 caractères';
    } elseif (empty($contenu) || strlen($contenu) < 10) {
        $error = 'Le contenu doit contenir au moins 10 caractères';
    } elseif (empty($categorie)) {
        $error = 'Veuillez sélectionner une catégorie';
    } else {
        $publication->setTitre($titre);
        $publication->setContenu($contenu);
        $publication->setCategorie($categorie);
        $publication->setTags($tags);
        
        if ($controller->updatePublication($publication)) {
            header('Location: forum.php?success=2');
            exit;
        } else {
            $error = 'Erreur lors de la mise à jour de la publication';
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Modifier la publication - Forum</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 2rem auto;">
        <h1>Modifier la publication</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="edit_publication.php?id=<?php echo $id; ?>" onsubmit="return validatePublicationForm()">
            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" id="titre" name="titre" class="form-control" required onblur="validateField('titre')" maxlength="255" value="<?php echo htmlspecialchars($publication->getTitre()); ?>">
                <span class="form-error" id="titreError"></span>
            </div>
            
            <div class="form-group">
                <label for="categorie" class="form-label">Catégorie *</label>
                <select id="categorie" name="categorie" class="form-control" required onchange="validateField('categorie')">
                    <option value="">Sélectionner...</option>
                    <option value="support" <?php echo ($publication->getCategorie() === 'support') ? 'selected' : ''; ?>>Soutien</option>
                    <option value="experience" <?php echo ($publication->getCategorie() === 'experience') ? 'selected' : ''; ?>>Expériences</option>
                    <option value="advice" <?php echo ($publication->getCategorie() === 'advice') ? 'selected' : ''; ?>>Conseils</option>
                    <option value="discussion" <?php echo ($publication->getCategorie() === 'discussion') ? 'selected' : ''; ?>>Discussion</option>
                </select>
                <span class="form-error" id="categorieError"></span>
            </div>
            
            <div class="form-group">
                <label for="contenu" class="form-label">Contenu *</label>
                <textarea id="contenu" name="contenu" class="form-control" rows="10" required onblur="validateField('contenu')" maxlength="5000"><?php echo htmlspecialchars($publication->getContenu()); ?></textarea>
                <span class="form-error" id="contenuError"></span>
                <small style="color: var(--color-text-light);">Caractères restants : <span id="charCount"><?php echo 5000 - strlen($publication->getContenu()); ?></span></small>
            </div>
            
            <div class="form-group">
                <label for="tags" class="form-label">Tags (séparés par des virgules)</label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="violence, discrimination, soutien" value="<?php echo htmlspecialchars($publication->getTags()); ?>">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="forum.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
    
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/forum.js"></script>
</body>
</html>

