<?php
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';
require_once dirname(__DIR__) . '/../models/Publication.php';

$controller = new PublicationController();
$message = '';
$error = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
            $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
            $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
            $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
            $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
            
            // Validation
            if (empty($titre) || strlen($titre) < 5) {
                $error = 'Le titre doit contenir au moins 5 caractères';
            } elseif (empty($contenu) || strlen($contenu) < 10) {
                $error = 'Le contenu doit contenir au moins 10 caractères';
            } elseif (empty($categorie)) {
                $error = 'Veuillez sélectionner une catégorie';
            } elseif (empty($auteur) || strlen($auteur) < 2) {
                $error = 'Le nom de l\'auteur doit contenir au moins 2 caractères';
            } else {
                $publication = new Publication();
                $publication->setTitre($titre);
                $publication->setContenu($contenu);
                $publication->setCategorie($categorie);
                $publication->setTags($tags);
                $publication->setAuteur($auteur);
                
                if ($_POST['action'] === 'create') {
                    $id = $controller->addPublication($publication);
                    if ($id) {
                        header('Location: publications.php?success=1');
                        exit;
                    } else {
                        $error = 'Erreur lors de la création de la publication';
                    }
                } else {
                    $publication->setIdPublication(intval($_POST['id']));
                    if ($controller->updatePublication($publication)) {
                        header('Location: publications.php?success=2');
                        exit;
                    } else {
                        $error = 'Erreur lors de la mise à jour de la publication';
                    }
                }
            }
        }
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) {
        $message = 'Publication créée avec succès !';
    } elseif ($_GET['success'] == 2) {
        $message = 'Publication mise à jour avec succès !';
    } elseif ($_GET['success'] == 3) {
        $message = 'Publication supprimée avec succès !';
    }
}

$publications = $controller->listPublications();
if ($publications === false) {
    $publications = array();
}

// Get publication for edit
$publicationToEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $publicationToEdit = $controller->getPublicationById(intval($_GET['id']));
    if (!$publicationToEdit) {
        $action = 'list';
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Publications - BackOffice</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
    <?php include 'template_header.php'; ?>
    
    <div class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h1>Gestion des Publications</h1>
                <a href="publications.php?action=create" class="btn btn-primary">➕ Nouvelle publication</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($action === 'create' || $action === 'edit'): ?>
                <div class="card">
                    <div class="card-header">
                        <h2><?php echo $action === 'create' ? 'Créer une publication' : 'Modifier la publication'; ?></h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="publications.php" onsubmit="return validatePublicationForm()">
                            <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $publicationToEdit->getIdPublication(); ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="auteur" class="form-label">Auteur *</label>
                                <input type="text" id="auteur" name="auteur" class="form-control" onblur="validateField('auteur')" value="<?php echo $action === 'edit' ? htmlspecialchars($publicationToEdit->getAuteur()) : (isset($_POST['auteur']) ? htmlspecialchars($_POST['auteur']) : ''); ?>">
                                <span class="form-error" id="auteurError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="titre" class="form-label">Titre *</label>
                                <input type="text" id="titre" name="titre" class="form-control" onblur="validateField('titre')" value="<?php echo $action === 'edit' ? htmlspecialchars($publicationToEdit->getTitre()) : (isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''); ?>">
                                <span class="form-error" id="titreError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select id="categorie" name="categorie" class="form-control" onchange="validateField('categorie')">
                                    <option value="">Sélectionner...</option>
                                    <option value="support" <?php echo (($action === 'edit' && $publicationToEdit->getCategorie() === 'support') || (isset($_POST['categorie']) && $_POST['categorie'] === 'support')) ? 'selected' : ''; ?>>Soutien</option>
                                    <option value="experience" <?php echo (($action === 'edit' && $publicationToEdit->getCategorie() === 'experience') || (isset($_POST['categorie']) && $_POST['categorie'] === 'experience')) ? 'selected' : ''; ?>>Expériences</option>
                                    <option value="advice" <?php echo (($action === 'edit' && $publicationToEdit->getCategorie() === 'advice') || (isset($_POST['categorie']) && $_POST['categorie'] === 'advice')) ? 'selected' : ''; ?>>Conseils</option>
                                    <option value="discussion" <?php echo (($action === 'edit' && $publicationToEdit->getCategorie() === 'discussion') || (isset($_POST['categorie']) && $_POST['categorie'] === 'discussion')) ? 'selected' : ''; ?>>Discussion</option>
                                </select>
                                <span class="form-error" id="categorieError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="contenu" class="form-label">Contenu *</label>
                                <textarea id="contenu" name="contenu" class="form-control" rows="10" onblur="validateField('contenu')"><?php echo $action === 'edit' ? htmlspecialchars($publicationToEdit->getContenu()) : (isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''); ?></textarea>
                                <span class="form-error" id="contenuError"></span>
                                <small style="color: var(--color-text-light);">Caractères restants : <span id="charCount"><?php echo $action === 'edit' ? (5000 - strlen($publicationToEdit->getContenu())) : '5000'; ?></span></small>
                            </div>
                            
                            <div class="form-group">
                                <label for="tags" class="form-label">Tags (séparés par des virgules)</label>
                                <input type="text" id="tags" name="tags" class="form-control" placeholder="violence, discrimination, soutien" value="<?php echo $action === 'edit' ? htmlspecialchars($publicationToEdit->getTags()) : (isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''); ?>">
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                <button type="submit" class="btn btn-primary"><?php echo $action === 'create' ? 'Créer' : 'Mettre à jour'; ?></button>
                                <a href="publications.php" class="btn btn-outline">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Catégorie</th>
                                <th>Date création</th>
                                <th>Likes</th>
                                <th>Commentaires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($publications)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem;">Aucune publication trouvée.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($publications as $pub): ?>
                                    <tr>
                                        <td><?php echo $pub->getIdPublication(); ?></td>
                                        <td><?php echo htmlspecialchars($pub->getTitre()); ?></td>
                                        <td><?php echo htmlspecialchars($pub->getAuteur()); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($pub->getCategorie()); ?></span></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pub->getDateCreation())); ?></td>
                                        <td><?php echo $pub->getNombreLikes(); ?></td>
                                        <td><?php echo $pub->getNombreCommentaires(); ?></td>
                                        <td>
                                            <a href="publications.php?action=edit&id=<?php echo $pub->getIdPublication(); ?>" class="btn btn-outline btn-sm">✏️ Modifier</a>
                                            <a href="delete_publication.php?id=<?php echo $pub->getIdPublication(); ?>" class="btn btn-outline btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?');">🗑️ Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'template_footer.php'; ?>
    
    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/forum.js"></script>
</body>
</html>

