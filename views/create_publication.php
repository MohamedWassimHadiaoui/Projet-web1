<?php
require_once dirname(__DIR__) . '/controllers/PublicationController.php';
require_once dirname(__DIR__) . '/models/Publication.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
    
    function containsOnlyLetters($text) {
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
    } elseif (empty($auteur) || strlen($auteur) < 2) {
        $error = 'Le nom de l\'auteur doit contenir au moins 2 caractères';
    } elseif (!containsOnlyLetters($auteur)) {
        $error = 'seulement des lettres';
    } else {
        $publication = new Publication();
        $publication->setTitre($titre);
        $publication->setContenu($contenu);
        $publication->setCategorie($categorie);
        $publication->setTags($tags);
        $publication->setAuteur($auteur);
        
        $controller = new PublicationController();
        $id = $controller->addPublication($publication);
        
        if ($id) {
            header('Location: forum.php?pending=1');
            exit;
        } else {
            $error = 'Erreur lors de la création de la publication';
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Créer une publication - Forum</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 2rem auto;">
        <a href="forum.php" class="btn btn-outline" style="margin-bottom: 1rem;">← Retour au forum</a>
        <h1>Créer une nouvelle publication</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="create_publication.php" onsubmit="return validatePublicationForm()">
            <div class="form-group">
                <label for="auteur" class="form-label">Votre nom *</label>
                <input type="text" id="auteur" name="auteur" class="form-control" 
                       onblur="validateField('auteur')" 
                       oninput="validateField('auteur')"
                       placeholder="Ex: Jean Dupont"
                       value="<?php echo isset($_POST['auteur']) ? htmlspecialchars($_POST['auteur']) : ''; ?>">
                <span class="form-error" id="auteurError"></span>
                <small style="color: var(--color-text-light);">seulement des lettres</small>
            </div>
            
            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" id="titre" name="titre" class="form-control" 
                       onblur="validateField('titre')" 
                       oninput="validateField('titre')"
                       placeholder="Ex: Besoin d'aide"
                       value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>">
                <span class="form-error" id="titreError"></span>
                <small style="color: var(--color-text-light);">seulement des lettres</small>
            </div>
            
            <div class="form-group">
                <label for="categorie" class="form-label">Catégorie *</label>
                <select id="categorie" name="categorie" class="form-control" onchange="validateField('categorie')">
                    <option value="">Sélectionner...</option>
                    <option value="support" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'support') ? 'selected' : ''; ?>>Soutien</option>
                    <option value="experience" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'experience') ? 'selected' : ''; ?>>Expériences</option>
                    <option value="advice" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'advice') ? 'selected' : ''; ?>>Conseils</option>
                    <option value="discussion" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'discussion') ? 'selected' : ''; ?>>Discussion</option>
                </select>
                <span class="form-error" id="categorieError"></span>
            </div>
            
            <div class="form-group">
                <label for="contenu" class="form-label">Contenu *</label>
                <textarea id="contenu" name="contenu" class="form-control" rows="10" onblur="validateField('contenu')"><?php echo isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''; ?></textarea>
                <span class="form-error" id="contenuError"></span>
                <small style="color: var(--color-text-light);">Caractères restants : <span id="charCount">5000</span></small>
            </div>
            
            <div class="form-group">
                <label for="tags" class="form-label">Tags (séparés par des virgules)</label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="violence, discrimination, soutien" value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Publier</button>
                <a href="forum.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
    
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/forum.js"></script>
</body>
</html>
