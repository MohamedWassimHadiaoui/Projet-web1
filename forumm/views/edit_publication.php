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
    
    // Fonction de validation : vérifier si le titre contient seulement des lettres
    function containsOnlyLetters($text) {
        // Expression régulière : seulement lettres (avec accents), espaces, tirets, apostrophes
        // PAS de chiffres ni caractères spéciaux
        return preg_match('/^[a-zA-Z\sàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ\-\']+$/', $text);
    }
    
    if (empty($titre) || strlen($titre) < 5) {
        $error = 'Le titre doit contenir au moins 5 caractères';
    } elseif (!containsOnlyLetters($titre)) {
        $error = 'seulement des lettres';
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
        <a href="forum.php" class="btn btn-outline" style="margin-bottom: 1rem;">← Retour au forum</a>
        <h1>Modifier la publication</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="edit_publication.php?id=<?php echo $id; ?>" onsubmit="return validatePublicationForm()">
            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" id="titre" name="titre" class="form-control" 
                       onblur="validateField('titre')" 
                       oninput="validateField('titre')"
                       placeholder="Ex: Besoin d'aide"
                       value="<?php echo htmlspecialchars($publication->getTitre()); ?>">
                <span class="form-error" id="titreError"></span>
                <small style="color: var(--color-text-light);">seulement des lettres</small>
            </div>
            
            <div class="form-group">
                <label for="categorie" class="form-label">Catégorie *</label>
                <select id="categorie" name="categorie" class="form-control" onchange="validateField('categorie')">
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
                <textarea id="contenu" name="contenu" class="form-control" rows="10" onblur="validateField('contenu')"><?php echo htmlspecialchars($publication->getContenu()); ?></textarea>
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

