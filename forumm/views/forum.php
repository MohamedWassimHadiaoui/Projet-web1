<?php
require_once dirname(__DIR__) . '/controllers/PublicationController.php';
require_once dirname(__DIR__) . '/controllers/CommentaireController.php';

$publicationController = new PublicationController();
$commentaireController = new CommentaireController();

$publications = $publicationController->listPublications();
if ($publications === false) {
    $publications = array();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - PeaceConnect</title>
    
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="forum.php" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
                <button class="navbar-toggle" aria-label="Menu">☰</button>
                <ul class="navbar-menu">
                    <li><a href="forum.php" class="active">Forum</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <?php if (isset($_GET['success'])): ?>
                <?php if ($_GET['success'] == 1): ?>
                    <div class="alert alert-success">Publication créée avec succès !</div>
                <?php elseif ($_GET['success'] == 2): ?>
                    <div class="alert alert-success">Publication mise à jour avec succès !</div>
                <?php elseif ($_GET['success'] == 3): ?>
                    <div class="alert alert-success">Publication supprimée avec succès !</div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">Une erreur s'est produite. Veuillez réessayer.</div>
            <?php endif; ?>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1>Forum de la communauté</h1>
                    <p style="color: var(--color-text-light);">Partagez vos expériences et échangez avec la communauté</p>
                </div>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="create_publication.php" class="btn btn-primary">
                        ➕ Créer un nouveau post
                    </a>
                    <a href="BackOffice/index.php" class="btn btn-outline">
                        🛡️ Administration
                    </a>
                </div>
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label class="form-label">Rechercher</label>
                    <input type="text" id="searchInput" class="form-control filter-input" placeholder="Rechercher dans les posts..." onkeyup="filterPosts()">
                </div>
                <div class="filter-group">
                    <label class="form-label">Catégorie</label>
                    <select id="categoryFilter" class="form-control" onchange="filterPosts()">
                        <option value="">Toutes les catégories</option>
                        <option value="support">Soutien</option>
                        <option value="experience">Expériences</option>
                        <option value="advice">Conseils</option>
                        <option value="discussion">Discussion</option>
                    </select>
                </div>
            </div>

            <div id="forumPosts">
                <?php if (empty($publications)): ?>
                    <div class="card">
                        <div class="card-body">
                            <p style="text-align: center; color: var(--color-text-light);">Aucune publication pour le moment. Soyez le premier à publier !</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($publications as $pub): ?>
                        <div class="card forum-post" style="margin-bottom: 1.5rem;" data-category="<?php echo htmlspecialchars($pub->getCategorie()); ?>" data-tags="<?php echo htmlspecialchars($pub->getTags()); ?>">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?php echo strtoupper(substr($pub->getAuteur(), 0, 2)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($pub->getAuteur()); ?></strong>
                                            <p style="font-size: 0.875rem; color: var(--color-text-light); margin: 0;"><?php echo date('d/m/Y H:i', strtotime($pub->getDateCreation())); ?></p>
                                        </div>
                                    </div>
                                    <div>
                                        <?php 
                                        $tags = explode(',', $pub->getTags());
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
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h3 style="margin: 0; flex: 1;"><?php echo htmlspecialchars($pub->getTitre()); ?></h3>
                                    <button type="button" 
                                            class="speech-btn" 
                                            title="Écouter la publication"
                                            data-titre="<?php echo htmlspecialchars($pub->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-contenu="<?php echo htmlspecialchars($pub->getContenu(), ENT_QUOTES, 'UTF-8'); ?>"
                                            onclick="speakPublicationFromButton(this); return false;">
                                        🔊
                                    </button>
                                </div>
                                <p><?php echo htmlspecialchars($pub->getContenu()); ?></p>
                                <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; justify-content: space-between;">
                                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <span class="btn btn-outline btn-sm">👍 <?php echo $pub->getNombreLikes(); ?></span>
                                        <a href="view_publication.php?id=<?php echo $pub->getIdPublication(); ?>" class="btn btn-outline btn-sm">💬 <?php echo $pub->getNombreCommentaires(); ?> commentaires</a>
                                    </div>
                                    <div>
                                        <a href="edit_publication.php?id=<?php echo $pub->getIdPublication(); ?>" class="btn btn-outline btn-sm" title="Modifier">✏️</a>
                                        <a href="delete_publication.php?id=<?php echo $pub->getIdPublication(); ?>" class="btn btn-outline btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?');" title="Supprimer">🗑️</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="background-color: var(--color-text); color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/forum.js"></script>
    <script>
    // Système de synthèse vocale global pour toutes les publications
    let globalSpeechState = {
        isSpeaking: false,
        currentUtterance: null,
        currentButton: null
    };
    
    /**
     * Fonction appelée par chaque bouton de synthèse vocale
     * Fonctionne pour TOUTES les publications
     */
    function speakPublicationFromButton(button) {
        // Récupérer les données depuis les attributs data
        const titre = button.getAttribute('data-titre') || '';
        const contenu = button.getAttribute('data-contenu') || '';
        
        console.log('=== FONCTION speakPublicationFromButton APPELÉE ===');
        console.log('Bouton:', button);
        console.log('Titre (longueur):', titre ? titre.length : 0);
        console.log('Contenu (longueur):', contenu ? contenu.length : 0);
        
        // Si on clique sur le même bouton et qu'on est en train de parler, arrêter
        if (globalSpeechState.isSpeaking && globalSpeechState.currentButton === button) {
            window.speechSynthesis.cancel();
            globalSpeechState.isSpeaking = false;
            globalSpeechState.currentUtterance = null;
            globalSpeechState.currentButton = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
            // Réinitialiser tous les autres boutons
            document.querySelectorAll('.speech-btn').forEach(btn => {
                if (btn !== button) {
                    btn.innerHTML = '🔊';
                    btn.classList.remove('speaking');
                }
            });
            console.log('Lecture arrêtée');
            return;
        }
        
        // Si on clique sur un autre bouton pendant la lecture, arrêter la précédente
        if (globalSpeechState.isSpeaking) {
            window.speechSynthesis.cancel();
            if (globalSpeechState.currentButton) {
                globalSpeechState.currentButton.innerHTML = '🔊';
                globalSpeechState.currentButton.classList.remove('speaking');
            }
        }
        
        speakPublicationSimple(button, titre, contenu);
    }
    
    /**
     * Fonction principale de synthèse vocale
     * Fonctionne pour toutes les publications
     */
    function speakPublicationSimple(button, titre, contenu) {
        // Vérifier l'API
        if (!('speechSynthesis' in window)) {
            alert('Synthèse vocale non supportée par votre navigateur');
            return;
        }
        
        // Nettoyer et préparer le texte
        function decodeHtml(html) {
            const txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        }
        
        let cleanTitre = titre ? decodeHtml(titre).trim() : '';
        let cleanContenu = contenu ? decodeHtml(contenu).trim() : '';
        
        // Supprimer les balises HTML restantes
        cleanContenu = cleanContenu.replace(/<[^>]*>/g, '');
        // Remplacer les sauts de ligne par des points
        cleanContenu = cleanContenu.replace(/\n+/g, '. ');
        cleanContenu = cleanContenu.replace(/\r+/g, '');
        
        console.log('Titre nettoyé:', cleanTitre);
        console.log('Contenu nettoyé:', cleanContenu.substring(0, 100));
        
        // Préparer le texte complet
        let fullText = '';
        if (cleanTitre) {
            fullText += cleanTitre + '. ';
        }
        if (cleanContenu) {
            fullText += cleanContenu;
        }
        
        console.log('Texte complet (longueur):', fullText.length);
        console.log('Texte complet:', fullText.substring(0, 200));
        
        if (!fullText.trim()) {
            alert('Aucun texte à lire');
            return;
        }
        
        // Créer l'énoncé avec le texte nettoyé
        globalSpeechState.currentUtterance = new SpeechSynthesisUtterance(fullText);
        globalSpeechState.currentUtterance.lang = 'fr-FR';
        globalSpeechState.currentUtterance.rate = 1.0;
        globalSpeechState.currentUtterance.pitch = 1.0;
        globalSpeechState.currentUtterance.volume = 1.0;
        
        // Trouver une voix française
        function getFrenchVoice() {
            const voices = window.speechSynthesis.getVoices();
            if (voices.length > 0) {
                const frenchVoice = voices.find(v => v.lang.startsWith('fr')) || voices[0];
                return frenchVoice;
            }
            return null;
        }
        
        // Fonction pour démarrer la lecture
        function startSpeaking() {
            const voice = getFrenchVoice();
            if (voice) {
                globalSpeechState.currentUtterance.voice = voice;
                console.log('Voix utilisée:', voice.name);
            }
            
            // Événements
            globalSpeechState.currentUtterance.onstart = function() {
                globalSpeechState.isSpeaking = true;
                globalSpeechState.currentButton = button;
                button.innerHTML = '🔇';
                button.classList.add('speaking');
                // Réinitialiser tous les autres boutons
                document.querySelectorAll('.speech-btn').forEach(btn => {
                    if (btn !== button) {
                        btn.innerHTML = '🔊';
                        btn.classList.remove('speaking');
                    }
                });
                console.log('✅ Lecture démarrée pour:', cleanTitre.substring(0, 30));
            };
            
            globalSpeechState.currentUtterance.onend = function() {
                globalSpeechState.isSpeaking = false;
                globalSpeechState.currentUtterance = null;
                globalSpeechState.currentButton = null;
                button.innerHTML = '🔊';
                button.classList.remove('speaking');
                console.log('✅ Lecture terminée');
            };
            
            globalSpeechState.currentUtterance.onerror = function(event) {
                console.error('❌ Erreur:', event.error);
                globalSpeechState.isSpeaking = false;
                globalSpeechState.currentUtterance = null;
                globalSpeechState.currentButton = null;
                button.innerHTML = '🔊';
                button.classList.remove('speaking');
                alert('Erreur lors de la lecture: ' + event.error);
            };
            
            // Lancer la lecture
            try {
                window.speechSynthesis.speak(globalSpeechState.currentUtterance);
                console.log('✅ Commande speak() envoyée');
            } catch (error) {
                console.error('❌ Erreur speak():', error);
                alert('Erreur: ' + error.message);
            }
        }
        
        // Attendre que les voix se chargent si nécessaire
        const voices = window.speechSynthesis.getVoices();
        if (voices.length === 0) {
            console.log('Attente du chargement des voix...');
            const voicesHandler = function() {
                console.log('Voix chargées:', window.speechSynthesis.getVoices().length);
                window.speechSynthesis.removeEventListener('voiceschanged', voicesHandler);
                startSpeaking();
            };
            window.speechSynthesis.addEventListener('voiceschanged', voicesHandler);
            // Forcer le rechargement
            window.speechSynthesis.getVoices();
        } else {
            startSpeaking();
        }
    }
    
    // Attendre que les voix se chargent au chargement de la page
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = function() {
            console.log('Voix disponibles:', window.speechSynthesis.getVoices().length);
        };
    }
    
    // S'assurer que tous les boutons sont initialisés après le chargement
    document.addEventListener('DOMContentLoaded', function() {
        const allButtons = document.querySelectorAll('.speech-btn');
        console.log('Nombre de boutons de synthèse vocale trouvés:', allButtons.length);
        allButtons.forEach(function(btn, index) {
            console.log('Bouton', index + 1, ':', btn.getAttribute('data-titre')?.substring(0, 30));
        });
    });
    </script>
</body>
</html>

