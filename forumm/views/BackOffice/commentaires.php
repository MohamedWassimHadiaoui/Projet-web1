<?php
require_once dirname(__DIR__) . '/../controllers/CommentaireController.php';
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';
require_once dirname(__DIR__) . '/../models/Commentaire.php';

$commentaireController = new CommentaireController();
$publicationController = new PublicationController();
$message = '';
$error = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
            $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
            $id_publication = isset($_POST['id_publication']) ? intval($_POST['id_publication']) : 0;
            
            // Validation
            if (empty($contenu) || strlen($contenu) < 3) {
                $error = 'Le commentaire doit contenir au moins 3 caractères';
            } elseif (empty($auteur) || strlen($auteur) < 2) {
                $error = 'Le nom de l\'auteur doit contenir au moins 2 caractères';
            } elseif ($id_publication <= 0) {
                $error = 'Veuillez sélectionner une publication';
            } else {
                $commentaire = new Commentaire();
                $commentaire->setContenu($contenu);
                $commentaire->setAuteur($auteur);
                $commentaire->setIdPublication($id_publication);
                
                if ($_POST['action'] === 'create') {
                    $id = $commentaireController->addCommentaire($commentaire);
                    if ($id) {
                        header('Location: commentaires.php?success=1');
                        exit;
                    } else {
                        $error = 'Erreur lors de la création du commentaire';
                    }
                } else {
                    $commentaire->setIdCommentaire(intval($_POST['id']));
                    if ($commentaireController->updateCommentaire($commentaire)) {
                        header('Location: commentaires.php?success=2');
                        exit;
                    } else {
                        $error = 'Erreur lors de la mise à jour du commentaire';
                    }
                }
            }
        }
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) {
        $message = 'Commentaire créé avec succès !';
    } elseif ($_GET['success'] == 2) {
        $message = 'Commentaire mis à jour avec succès !';
    } elseif ($_GET['success'] == 3) {
        $message = 'Commentaire supprimé avec succès !';
    }
}

// Get all publications for dropdown
$publications = $publicationController->listPublications();
if ($publications === false) {
    $publications = array();
}

// Get all commentaires
$allCommentaires = array();
foreach ($publications as $pub) {
    $coms = $commentaireController->listCommentairesByPublication($pub->getIdPublication());
    if (is_array($coms)) {
        foreach ($coms as $com) {
            $allCommentaires[] = array(
                'commentaire' => $com,
                'publication' => $pub
            );
        }
    }
}

// Get commentaire for edit
$commentaireToEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $commentaireToEdit = $commentaireController->getCommentaireById(intval($_GET['id']));
    if (!$commentaireToEdit) {
        $action = 'list';
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commentaires - BackOffice</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
    <?php include 'template_header.php'; ?>
    
    <div class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h1>Gestion des Commentaires</h1>
                <a href="commentaires.php?action=create" class="btn btn-primary">➕ Nouveau commentaire</a>
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
                        <h2><?php echo $action === 'create' ? 'Créer un commentaire' : 'Modifier le commentaire'; ?></h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="commentaires.php" onsubmit="return validateCommentForm()">
                            <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $commentaireToEdit->getIdCommentaire(); ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="id_publication" class="form-label">Publication *</label>
                                <select id="id_publication" name="id_publication" class="form-control" onchange="validatePublicationField()" <?php echo $action === 'edit' ? 'disabled' : ''; ?>>
                                    <option value="">Sélectionner une publication...</option>
                                    <?php foreach ($publications as $pub): ?>
                                        <option value="<?php echo $pub->getIdPublication(); ?>" <?php echo (($action === 'edit' && $commentaireToEdit->getIdPublication() == $pub->getIdPublication()) || (isset($_POST['id_publication']) && $_POST['id_publication'] == $pub->getIdPublication())) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pub->getTitre()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id_publication" value="<?php echo $commentaireToEdit->getIdPublication(); ?>">
                                <?php endif; ?>
                                <span class="form-error" id="id_publicationError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="auteur" class="form-label">Auteur *</label>
                                <input type="text" id="auteur" name="auteur" class="form-control" onblur="validateField('auteur')" value="<?php echo $action === 'edit' ? htmlspecialchars($commentaireToEdit->getAuteur()) : (isset($_POST['auteur']) ? htmlspecialchars($_POST['auteur']) : ''); ?>">
                                <span class="form-error" id="auteurError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="contenu" class="form-label">Contenu *</label>
                                <textarea id="contenu" name="contenu" class="form-control" rows="6" onblur="validateCommentField('contenu')"><?php echo $action === 'edit' ? htmlspecialchars($commentaireToEdit->getContenu()) : (isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''); ?></textarea>
                                <span class="form-error" id="contenuError"></span>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                <button type="submit" class="btn btn-primary"><?php echo $action === 'create' ? 'Créer' : 'Mettre à jour'; ?></button>
                                <a href="commentaires.php" class="btn btn-outline">Annuler</a>
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
                                <th>Publication</th>
                                <th>Auteur</th>
                                <th>Contenu</th>
                                <th>Date création</th>
                                <th>Likes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allCommentaires)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">Aucun commentaire trouvé.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allCommentaires as $item): 
                                    $com = $item['commentaire'];
                                    $pub = $item['publication'];
                                ?>
                                    <tr>
                                        <td><?php echo $com->getIdCommentaire(); ?></td>
                                        <td><?php echo htmlspecialchars($pub->getTitre()); ?></td>
                                        <td><?php echo htmlspecialchars($com->getAuteur()); ?></td>
                                        <td><?php echo htmlspecialchars(substr($com->getContenu(), 0, 50)) . (strlen($com->getContenu()) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($com->getDateCreation())); ?></td>
                                        <td><?php echo $com->getNombreLikes(); ?></td>
                                        <td>
                                            <a href="commentaires.php?action=edit&id=<?php echo $com->getIdCommentaire(); ?>" class="btn btn-outline btn-sm">✏️ Modifier</a>
                                            <a href="delete_commentaire.php?id=<?php echo $com->getIdCommentaire(); ?>" class="btn btn-outline btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">🗑️ Supprimer</a>
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
    <script>
        function validatePublicationField() {
            const idPublication = document.getElementById('id_publication');
            const idPublicationError = document.getElementById('id_publicationError');
            
            if (idPublication && !idPublication.value) {
                idPublication.classList.add('error');
                if (idPublicationError) {
                    idPublicationError.classList.add('show');
                    idPublicationError.textContent = 'Veuillez sélectionner une publication';
                }
                return false;
            } else if (idPublication) {
                idPublication.classList.remove('error');
                if (idPublicationError) {
                    idPublicationError.classList.remove('show');
                }
                return true;
            }
            return true;
        }
        
        function validateCommentForm() {
            let isValid = true;
            
            if (!validateField('auteur')) isValid = false;
            if (!validateCommentField('contenu')) isValid = false;
            if (!validatePublicationField()) isValid = false;
            
            return isValid;
        }
    </script>
</body>
</html>

