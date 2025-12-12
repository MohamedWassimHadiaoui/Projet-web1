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
    <style>
       
        .translate-dropdown {
            position: relative;
            display: inline-block;
        }
        .translate-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            padding: 0;
            border: 2px solid #6366f1;
            background-color: white;
            color: #6366f1;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.15s ease-in-out;
        }
        .translate-btn:hover {
            background-color: #6366f1;
            color: white;
            transform: scale(1.1);
        }
        .translate-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 5px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            min-width: 160px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .translate-menu.show {
            display: block !important;
        }
        .translate-menu button {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 12px 16px;
            border: none;
            background: white;
            text-align: left;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .translate-menu button:hover {
            background: #eef2ff;
            color: #4f46e5;
        }
        .translating {
            opacity: 0.6;
            pointer-events: none;
        }
        .translated-badge {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 8px;
        }
        /* Style pour texte arabe */
        .rtl-text {
            direction: rtl;
            text-align: right;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        }
    </style>
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
            <?php if (isset($_GET['pending'])): ?>
                <div class="alert" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b;">
                    <strong>⏳ Publication soumise avec succès !</strong>
                    <p style="margin: 0.5rem 0 0 0;">Votre publication est en attente de validation par un administrateur. Elle sera visible une fois approuvée.</p>
                </div>
            <?php endif; ?>
            
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
                                    <h3 style="margin: 0; flex: 1;" id="titre-<?php echo $pub->getIdPublication(); ?>"><?php echo htmlspecialchars($pub->getTitre()); ?></h3>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <!-- Boutons de traduction -->
                                        <div class="translate-dropdown" id="dropdown-<?php echo $pub->getIdPublication(); ?>">
                                            <button type="button" class="translate-btn" title="Traduire" onclick="toggleTranslateMenu(<?php echo $pub->getIdPublication(); ?>); event.stopPropagation();">
                                                🌐
                                            </button>
                                            <div class="translate-menu" id="menu-<?php echo $pub->getIdPublication(); ?>">
                                                <button type="button" onclick="closeMenuAndTranslate(<?php echo $pub->getIdPublication(); ?>, 'en');">
                                                    🇬🇧 English
                                                </button>
                                                <button type="button" onclick="closeMenuAndTranslate(<?php echo $pub->getIdPublication(); ?>, 'ar');">
                                                    🇸🇦 العربية
                                                </button>
                                                <button type="button" onclick="closeMenuAndReset(<?php echo $pub->getIdPublication(); ?>);">
                                                    🇫🇷 Original
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Bouton synthèse vocale -->
                                        <button type="button" 
                                                class="speech-btn" 
                                                title="Écouter la publication"
                                                data-titre="<?php echo htmlspecialchars($pub->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"
                                                data-contenu="<?php echo htmlspecialchars($pub->getContenu(), ENT_QUOTES, 'UTF-8'); ?>"
                                                onclick="speakPublicationFromButton(this); return false;">
                                            🔊
                                        </button>
                                    </div>
                                </div>
                                <p id="contenu-<?php echo $pub->getIdPublication(); ?>" 
                                   data-original="<?php echo htmlspecialchars($pub->getContenu(), ENT_QUOTES, 'UTF-8'); ?>"
                                   data-original-titre="<?php echo htmlspecialchars($pub->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($pub->getContenu()); ?></p>
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
    let globalSpeechState = {
        isSpeaking: false,
        currentUtterance: null,
        currentButton: null
    };
    
    function speakPublicationFromButton(button) {
        const titre = button.getAttribute('data-titre') || '';
        const contenu = button.getAttribute('data-contenu') || '';
        
        if (globalSpeechState.isSpeaking && globalSpeechState.currentButton === button) {
            window.speechSynthesis.cancel();
            globalSpeechState.isSpeaking = false;
            globalSpeechState.currentUtterance = null;
            globalSpeechState.currentButton = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
            return;
        }
        
        if (globalSpeechState.isSpeaking) {
            window.speechSynthesis.cancel();
            if (globalSpeechState.currentButton) {
                globalSpeechState.currentButton.innerHTML = '🔊';
                globalSpeechState.currentButton.classList.remove('speaking');
            }
        }
        
        speakPublicationSimple(button, titre, contenu);
    }
    
    function speakPublicationSimple(button, titre, contenu) {
        if (!('speechSynthesis' in window)) {
            alert('Synthèse vocale non supportée par votre navigateur');
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
        if (cleanTitre) fullText += cleanTitre + '. ';
        if (cleanContenu) fullText += cleanContenu;
        
        if (!fullText.trim()) {
            alert('Aucun texte à lire');
            return;
        }
        
        globalSpeechState.currentUtterance = new SpeechSynthesisUtterance(fullText);
        globalSpeechState.currentUtterance.lang = 'fr-FR';
        globalSpeechState.currentUtterance.rate = 1.0;
        globalSpeechState.currentUtterance.pitch = 1.0;
        globalSpeechState.currentUtterance.volume = 1.0;
        
        const voices = window.speechSynthesis.getVoices();
        if (voices.length > 0) {
            const frenchVoice = voices.find(v => v.lang.startsWith('fr')) || voices[0];
            globalSpeechState.currentUtterance.voice = frenchVoice;
        }
        
        globalSpeechState.currentUtterance.onstart = function() {
            globalSpeechState.isSpeaking = true;
            globalSpeechState.currentButton = button;
            button.innerHTML = '🔇';
            button.classList.add('speaking');
        };
        
        globalSpeechState.currentUtterance.onend = function() {
            globalSpeechState.isSpeaking = false;
            globalSpeechState.currentUtterance = null;
            globalSpeechState.currentButton = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
        };
        
        globalSpeechState.currentUtterance.onerror = function(event) {
            globalSpeechState.isSpeaking = false;
            globalSpeechState.currentUtterance = null;
            globalSpeechState.currentButton = null;
            button.innerHTML = '🔊';
            button.classList.remove('speaking');
            alert('Erreur: ' + event.error);
        };
        
        try {
            window.speechSynthesis.speak(globalSpeechState.currentUtterance);
        } catch (error) {
            alert('Erreur: ' + error.message);
        }
    }
    
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = function() {};
    }
    
    const translationCache = {};
    
    function toggleTranslateMenu(pubId) {
        document.querySelectorAll('.translate-menu').forEach(function(menu) {
            if (menu.id !== 'menu-' + pubId) {
                menu.classList.remove('show');
            }
        });
        const menu = document.getElementById('menu-' + pubId);
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.translate-dropdown')) {
            document.querySelectorAll('.translate-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });
    
    function closeMenuAndTranslate(pubId, lang) {
        const menu = document.getElementById('menu-' + pubId);
        if (menu) {
            menu.classList.remove('show');
        }
        translatePublication(pubId, lang);
    }
    
    function closeMenuAndReset(pubId) {
        const menu = document.getElementById('menu-' + pubId);
        if (menu) {
            menu.classList.remove('show');
        }
        resetTranslation(pubId);
        if (typeof showToast === 'function') {
            showToast('Texte original restauré', 'success', 2000);
        }
    }
    
    function decodeHtmlEntities(html) {
        const txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
    }
    
    async function translateText(text, targetLang) {
        const cacheKey = text + '_' + targetLang;
        if (translationCache[cacheKey]) {
            return translationCache[cacheKey];
        }
        
        const sourceLang = 'fr';
        const url = 'https://api.mymemory.translated.net/get?q=' + 
                    encodeURIComponent(text) + 
                    '&langpair=' + sourceLang + '|' + targetLang;
        
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            const data = await response.json();
            if (data.responseStatus === 200 && data.responseData) {
                const translatedText = data.responseData.translatedText;
                translationCache[cacheKey] = translatedText;
                return translatedText;
            } else {
                throw new Error('Erreur API: ' + (data.responseDetails || 'Traduction impossible'));
            }
        } catch (error) {
            console.error('Erreur de traduction:', error.message);
            throw error;
        }
    }
    
    async function translatePublication(pubId, targetLang) {
        const titreElement = document.getElementById('titre-' + pubId);
        const contenuElement = document.getElementById('contenu-' + pubId);
        
        if (!titreElement || !contenuElement) {
            if (typeof showToast === 'function') {
                showToast('Erreur: Publication non trouvée', 'error', 3000);
            } else {
                alert('Erreur: Publication non trouvée');
            }
            return;
        }
        
        const originalTitre = decodeHtmlEntities(contenuElement.getAttribute('data-original-titre') || '');
        const originalContenu = decodeHtmlEntities(contenuElement.getAttribute('data-original') || '');
        
        if (!originalTitre && !originalContenu) {
            if (typeof showToast === 'function') {
                showToast('Aucun texte à traduire', 'error', 3000);
            }
            return;
        }
        
        titreElement.classList.add('translating');
        contenuElement.classList.add('translating');
        
        const langName = targetLang === 'ar' ? 'arabe' : 'anglais';
        const langFlag = targetLang === 'ar' ? '🇸🇦' : '🇬🇧';
        titreElement.innerHTML = '⏳ Traduction en ' + langName + '...';
        
        try {
            const [translatedTitre, translatedContenu] = await Promise.all([
                translateText(originalTitre, targetLang),
                translateText(originalContenu, targetLang)
            ]);
            
            const badge = '<span class="translated-badge">' + langFlag + ' ' + 
                         (targetLang === 'ar' ? 'العربية' : 'English') + '</span>';
            titreElement.innerHTML = translatedTitre + ' ' + badge;
            contenuElement.textContent = translatedContenu;
            
            if (targetLang === 'ar') {
                titreElement.classList.add('rtl-text');
                contenuElement.classList.add('rtl-text');
            } else {
                titreElement.classList.remove('rtl-text');
                contenuElement.classList.remove('rtl-text');
            }
            
            if (typeof showToast === 'function') {
                showToast('Traduction en ' + langName + ' effectuée !', 'success', 2000);
            }
            
        } catch (error) {
            titreElement.textContent = originalTitre;
            contenuElement.textContent = originalContenu;
            
            if (typeof showToast === 'function') {
                showToast('Erreur de traduction: ' + error.message, 'error', 4000);
            } else {
                alert('Erreur lors de la traduction. Veuillez réessayer.');
            }
            console.error('Erreur translatePublication:', error);
        }
        
        titreElement.classList.remove('translating');
        contenuElement.classList.remove('translating');
    }
    
    function resetTranslation(pubId) {
        const titreElement = document.getElementById('titre-' + pubId);
        const contenuElement = document.getElementById('contenu-' + pubId);
        
        if (!titreElement || !contenuElement) {
            return;
        }
        
        const originalTitre = decodeHtmlEntities(contenuElement.getAttribute('data-original-titre') || '');
        const originalContenu = decodeHtmlEntities(contenuElement.getAttribute('data-original') || '');
        
        titreElement.textContent = originalTitre;
        contenuElement.textContent = originalContenu;
        
        titreElement.classList.remove('rtl-text');
        contenuElement.classList.remove('rtl-text');
    }
    </script>
</body>
</html>

