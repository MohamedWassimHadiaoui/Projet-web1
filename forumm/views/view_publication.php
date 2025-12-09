<?php
require_once dirname(__DIR__) . '/controllers/PublicationController.php';
require_once dirname(__DIR__) . '/controllers/CommentaireController.php';
require_once dirname(__DIR__) . '/models/Commentaire.php';

$publicationController = new PublicationController();
$commentaireController = new CommentaireController();

if (!isset($_GET['id'])) {
    header('Location: forum.php');
    exit;
}

$id = intval($_GET['id']);
$publication = $publicationController->getPublicationById($id);

if (!$publication) {
    header('Location: forum.php');
    exit;
}

$commentaires = $commentaireController->listCommentairesByPublication($id);
if ($commentaires === false) {
    $commentaires = array();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
    $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
    
    function containsOnlyLetters($text) {
        return preg_match('/^[a-zA-Z\sàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ\-\']+$/', $text);
    }
    
    if (empty($contenu) || strlen($contenu) < 3) {
        $error = 'Le commentaire doit contenir au moins 3 caractères';
    } elseif (empty($auteur) || strlen($auteur) < 2) {
        $error = 'Le nom doit contenir au moins 2 caractères';
    } elseif (!containsOnlyLetters($auteur)) {
        $error = 'seulement des lettres';
    } else {
        $commentaire = new Commentaire();
        $commentaire->setIdPublication($id);
        $commentaire->setContenu($contenu);
        $commentaire->setAuteur($auteur);
        
        if ($commentaireController->addCommentaire($commentaire)) {
            header('Location: view_publication.php?id=' . $id . '&success=1');
            exit;
        } else {
            $error = 'Erreur lors de l\'ajout du commentaire';
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo htmlspecialchars($publication->getTitre()); ?> - Forum</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>
    <div class="container" style="max-width: 900px; margin: 2rem auto;">
        <a href="forum.php" class="btn btn-outline" style="margin-bottom: 1rem;">← Retour au forum</a>
        
        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] == 1): ?>
                <div class="alert alert-success">Commentaire ajouté avec succès !</div>
            <?php elseif ($_GET['success'] == 2): ?>
                <div class="alert alert-success">Commentaire supprimé avec succès !</div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">Une erreur s'est produite. Veuillez réessayer.</div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                            <?php echo strtoupper(substr($publication->getAuteur(), 0, 2)); ?>
                        </div>
                        <div>
                            <strong><?php echo htmlspecialchars($publication->getAuteur()); ?></strong>
                            <p style="font-size: 0.875rem; color: var(--color-text-light); margin: 0;"><?php echo date('d/m/Y H:i', strtotime($publication->getDateCreation())); ?></p>
                        </div>
                    </div>
                    <div>
                        <?php 
                        $tags = explode(',', $publication->getTags());
                        foreach ($tags as $tag): 
                            if (!empty(trim($tag))):
                        ?>
                            <span class="tag tag-primary">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <h2 style="margin: 0; flex: 1;"><?php echo htmlspecialchars($publication->getTitre()); ?></h2>
                    <button type="button" 
                            class="speech-btn" 
                            title="Écouter la publication"
                            data-titre="<?php echo htmlspecialchars($publication->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"
                            data-contenu="<?php echo htmlspecialchars($publication->getContenu(), ENT_QUOTES, 'UTF-8'); ?>"
                            onclick="speakPublicationFromButton(this); return false;">
                        🔊
                    </button>
                </div>
                <p><?php echo nl2br(htmlspecialchars($publication->getContenu())); ?></p>
            </div>
        </div>
        
        <div style="margin-top: 2rem;">
            <h3>Commentaires (<?php echo count($commentaires); ?>)</h3>
            
            <?php if (empty($commentaires)): ?>
                <p style="text-align: center; color: var(--color-text-light); padding: 2rem;">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
            <?php else: ?>
                <?php foreach ($commentaires as $com): ?>
                    <div class="card" style="margin-bottom: 1rem;">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem;">
                                        <?php echo strtoupper(substr($com->getAuteur(), 0, 2)); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($com->getAuteur()); ?></strong>
                                        <p style="font-size: 0.75rem; color: var(--color-text-light); margin: 0;"><?php echo date('d/m/Y H:i', strtotime($com->getDateCreation())); ?></p>
                                    </div>
                                </div>
                                <a href="delete_commentaire.php?id=<?php echo $com->getIdCommentaire(); ?>&id_publication=<?php echo $id; ?>" class="btn btn-outline btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');" title="Supprimer">🗑️</a>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($com->getContenu())); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="card" style="margin-top: 2rem;">
                <div class="card-body">
                    <h4>Ajouter un commentaire</h4>
                    <form method="POST" action="view_publication.php?id=<?php echo $id; ?>" onsubmit="return validateCommentForm()">
                        <input type="hidden" name="action" value="add_comment">
                        <div class="form-group">
                            <label for="auteur" class="form-label">Votre nom *</label>
                            <input type="text" id="auteur" name="auteur" class="form-control" 
                                   onblur="validateField('auteur')" 
                                   oninput="validateField('auteur')"
                                   placeholder="Ex: Jean Dupont">
                            <span class="form-error" id="auteurError"></span>
                            <small style="color: var(--color-text-light);">seulement des lettres</small>
                        </div>
                        <div class="form-group">
                            <label for="contenu" class="form-label">Votre commentaire *</label>
                            <textarea id="contenu" name="contenu" class="form-control" rows="4" onblur="validateField('contenu')"></textarea>
                            <span class="form-error" id="contenuError"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Publier le commentaire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/forum.js"></script>
    <script>
    let isSpeaking = false;
    let currentUtterance = null;
    
    function speakPublicationFromButton(button) {
        const titre = button.getAttribute('data-titre') || '';
        const contenu = button.getAttribute('data-contenu') || '';
        speakPublicationSimple(button, titre, contenu);
    }
    
    function speakPublicationSimple(button, titre, contenu) {
        if (isSpeaking && currentUtterance) {
            window.speechSynthesis.cancel();
            isSpeaking = false;
            currentUtterance = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
            return;
        }
        
        if (!('speechSynthesis' in window)) {
            alert('Synthèse vocale non supportée');
            return;
        }
        
        function decodeHtml(html) {
            const txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        }
        
        let cleanTitre = titre ? decodeHtml(titre).trim() : '';
        let cleanContenu = contenu ? decodeHtml(contenu).trim() : '';
        
        cleanContenu = cleanContenu.replace(/<[^>]*>/g, '');
        cleanContenu = cleanContenu.replace(/\n+/g, '. ');
        cleanContenu = cleanContenu.replace(/\r+/g, '');
        cleanContenu = cleanContenu.replace(/\s+/g, ' ');
        
        let fullText = '';
        if (cleanTitre) {
            fullText += cleanTitre + '. ';
        }
        if (cleanContenu) {
            fullText += cleanContenu;
        }
        
        if (!fullText.trim()) {
            alert('Aucun texte à lire');
            return;
        }
        
        currentUtterance = new SpeechSynthesisUtterance(fullText);
        currentUtterance.lang = 'fr-FR';
        currentUtterance.rate = 1.0;
        currentUtterance.pitch = 1.0;
        currentUtterance.volume = 1.0;
        
        const voices = window.speechSynthesis.getVoices();
        if (voices.length > 0) {
            const frenchVoice = voices.find(v => v.lang.startsWith('fr')) || voices[0];
            currentUtterance.voice = frenchVoice;
        }
        
        currentUtterance.onstart = function() {
            isSpeaking = true;
            button.innerHTML = '🔇';
            button.classList.add('speaking');
        };
        
        currentUtterance.onend = function() {
            isSpeaking = false;
            currentUtterance = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
        };
        
        currentUtterance.onerror = function(event) {
            isSpeaking = false;
            currentUtterance = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
            alert('Erreur: ' + event.error);
        };
        
        try {
            window.speechSynthesis.speak(currentUtterance);
        } catch (error) {
            alert('Erreur: ' + error.message);
        }
    }
    
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = function() {};
    }
    </script>
</body>
</html>
