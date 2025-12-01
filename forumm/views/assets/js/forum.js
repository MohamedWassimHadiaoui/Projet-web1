document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du forum...');
    initCharCounters();
    initFilters();
    initSpecialCharsValidation();
    
    // Vérifier que l'API Speech Synthesis est disponible
    if ('speechSynthesis' in window) {
        console.log('API Speech Synthesis disponible');
        // Ne pas appeler initSpeechButtons() car les boutons utilisent onclick dans forum.php
        // Les boutons sont gérés par le script inline dans forum.php
        
        // Attendre un peu pour que les voix se chargent
        setTimeout(function() {
            const voices = window.speechSynthesis.getVoices();
            console.log('Voix disponibles après chargement:', voices.length);
            if (voices.length === 0) {
                console.warn('Aucune voix disponible. La synthèse vocale peut ne pas fonctionner.');
            } else {
                console.log('Voix disponibles:', voices.map(v => v.name + ' (' + v.lang + ')').join(', '));
            }
        }, 1000);
    } else {
        console.error('API Speech Synthesis non disponible dans ce navigateur');
        // Désactiver les boutons
        const speechButtons = document.querySelectorAll('.speech-btn');
        speechButtons.forEach(function(btn) {
            btn.disabled = true;
            btn.title = 'Synthèse vocale non supportée';
            btn.style.opacity = '0.5';
        });
    }
});

/**
 * Initialise la validation en temps réel pour détecter les caractères spéciaux
 * Suit le pattern du cours JavaScript avec addEventListener
 * Affiche le message "pas de caractère spécial" si des caractères spéciaux sont détectés
 */
function initSpecialCharsValidation() {
    // Sélectionner les champs titre et auteur
    const titreField = document.getElementById('titre');
    const auteurField = document.getElementById('auteur');
    
    // Fonction pour valider et afficher le message d'erreur en temps réel
    function validateSpecialChars(event) {
        const field = event.target;
        const fieldId = field.id;
        
        // Valider le champ (cela va vérifier les caractères spéciaux et afficher le message)
        validateField(fieldId);
    }
    
    // Ajouter l'événement input pour validation en temps réel
    // La validation s'exécute à chaque caractère saisi
    if (titreField) {
        titreField.addEventListener('input', validateSpecialChars);
    }
    
    if (auteurField) {
        auteurField.addEventListener('input', validateSpecialChars);
    }
}

function initCharCounters() {
    const contenu = document.getElementById('contenu');
    const charCount = document.getElementById('charCount');
    
    if (contenu && charCount) {
        const maxLength = 5000;
        contenu.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            charCount.textContent = remaining;
            
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
                charCount.textContent = 0;
            }
        });
    }
}

function validatePublicationForm() {
    console.log('=== VALIDATION DU FORMULAIRE DE PUBLICATION ===');
    let isValid = true;
    
    // Récupérer les champs
    const titreField = document.getElementById('titre');
    const auteurField = document.getElementById('auteur');
    const categorieField = document.getElementById('categorie');
    const contenuField = document.getElementById('contenu');
    
    // Vérifier que les fonctions de validation sont disponibles
    if (typeof validateNoSpecialChars === 'undefined') {
        console.error('❌ ERREUR: La fonction validateNoSpecialChars n\'est pas disponible');
        alert('Erreur de validation. Veuillez recharger la page.');
        return false;
    }
    
    if (typeof validateOnlyLetters === 'undefined') {
        console.error('❌ ERREUR: La fonction validateOnlyLetters n\'est pas disponible');
        alert('Erreur de validation. Veuillez recharger la page.');
        return false;
    }
    
    // VALIDATION DU TITRE - Seulement des lettres
    if (titreField) {
        const titreValue = titreField.value.trim();
        console.log('Titre saisi:', titreValue);
        
        if (!titreValue) {
            isValid = false;
            validateField('titre');
            console.log('❌ Titre vide');
        } else {
            // Vérifier que c'est seulement des lettres (pas de chiffres ni caractères spéciaux)
            const titreLettersValidation = validateOnlyLetters(titreValue);
            if (!titreLettersValidation.valid) {
                isValid = false;
                validateField('titre');
                console.log('❌ Le titre doit contenir seulement des lettres:', titreValue);
            } else if (!validateField('titre')) {
                isValid = false;
                console.log('❌ Validation titre échouée');
            } else {
                console.log('✅ Titre valide');
            }
        }
    }
    
    // VALIDATION DE L'AUTEUR (NOM) - Seulement des lettres
    if (auteurField) {
        const auteurValue = auteurField.value.trim();
        console.log('Nom saisi:', auteurValue);
        
        if (!auteurValue) {
            isValid = false;
            validateField('auteur');
            console.log('❌ Nom vide');
        } else {
            // Vérifier que c'est seulement des lettres (pas de chiffres ni caractères spéciaux)
            const auteurLettersValidation = validateOnlyLetters(auteurValue);
            if (!auteurLettersValidation.valid) {
                isValid = false;
                validateField('auteur');
                console.log('❌ Le nom doit contenir seulement des lettres:', auteurValue);
            } else if (!validateField('auteur')) {
                isValid = false;
                console.log('❌ Validation nom échouée');
            } else {
                console.log('✅ Nom valide');
            }
        }
    }
    
    // VALIDATION DE LA CATÉGORIE
    if (!validateField('categorie')) {
        isValid = false;
        console.log('❌ Validation catégorie échouée');
    } else {
        console.log('✅ Catégorie valide');
    }
    
    // VALIDATION DU CONTENU
    if (!validateField('contenu')) {
        isValid = false;
        console.log('❌ Validation contenu échouée');
    } else {
        console.log('✅ Contenu valide');
    }
    
    // BLOQUER LA SOUMISSION SI INVALIDE
    if (!isValid) {
        console.log('❌ FORMULAIRE INVALIDE - Soumission BLOQUÉE');
        alert('Veuillez corriger les erreurs dans le formulaire. Les caractères spéciaux ne sont pas autorisés dans le nom et le titre.');
        return false; // BLOQUE la soumission
    }
    
    console.log('✅ FORMULAIRE VALIDE - Soumission autorisée');
    return true; // Autorise la soumission
}

function validateCommentForm() {
    let isValid = true;
    
    if (!validateField('auteur')) isValid = false;
    if (!validateCommentField('contenu')) isValid = false;
    
    return isValid;
}

function validateCommentField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId + 'Error');
    
    if (!field) return true;
    
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    if (fieldId === 'contenu') {
        if (!value) {
            isValid = false;
            errorMessage = 'Le commentaire est obligatoire';
        } else if (value.length < 3) {
            isValid = false;
            errorMessage = 'Le commentaire doit contenir au moins 3 caractères';
        } else if (value.length > 1000) {
            isValid = false;
            errorMessage = 'Le commentaire ne peut pas dépasser 1000 caractères';
        }
    }
    
    if (errorElement) {
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.textContent = errorMessage;
        }
    }
    
    return isValid;
}

function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId + 'Error');
    
    if (!field) return true;
    
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire';
    }
    
    if (fieldId === 'titre') {
        if (!value) {
            isValid = false;
            errorMessage = 'Le titre est obligatoire';
        } else if (value.length < 5) {
            isValid = false;
            errorMessage = 'Le titre doit contenir au moins 5 caractères';
        } else if (value.length > 255) {
            isValid = false;
            errorMessage = 'Le titre ne peut pas dépasser 255 caractères';
        } else {
            // Validation : seulement des lettres (pas de chiffres ni caractères spéciaux)
            const lettersValidation = validateOnlyLetters(value);
            if (!lettersValidation.valid) {
                isValid = false;
                errorMessage = lettersValidation.message;
            }
        }
    }
    
    if (fieldId === 'contenu') {
        if (!value) {
            isValid = false;
            errorMessage = 'Le contenu est obligatoire';
        } else if (value.length < 10) {
            isValid = false;
            errorMessage = 'Le contenu doit contenir au moins 10 caractères';
        } else if (value.length > 5000) {
            isValid = false;
            errorMessage = 'Le contenu ne peut pas dépasser 5000 caractères';
        }
    }
    
    if (fieldId === 'categorie') {
        if (!value) {
            isValid = false;
            errorMessage = 'Veuillez sélectionner une catégorie';
        }
    }
    
    if (fieldId === 'auteur') {
        if (!value) {
            isValid = false;
            errorMessage = 'Le nom est obligatoire';
        } else if (value.length < 2) {
            isValid = false;
            errorMessage = 'Le nom doit contenir au moins 2 caractères';
        } else if (value.length > 100) {
            isValid = false;
            errorMessage = 'Le nom ne peut pas dépasser 100 caractères';
        } else {
            // Validation : seulement des lettres (pas de chiffres ni caractères spéciaux)
            const lettersValidation = validateOnlyLetters(value);
            if (!lettersValidation.valid) {
                isValid = false;
                errorMessage = lettersValidation.message;
            }
        }
    }
    
    if (errorElement) {
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.textContent = errorMessage;
        }
    }
    
    return isValid;
}

function filterPosts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const posts = document.querySelectorAll('.forum-post');
    
    posts.forEach(function(post) {
        const postCategory = post.getAttribute('data-category');
        const postText = post.textContent.toLowerCase();
        
        let matches = true;
        
        if (searchTerm && !postText.includes(searchTerm)) {
            matches = false;
        }
        
        if (category && postCategory !== category) {
            matches = false;
        }
        
        if (matches) {
            post.style.display = '';
        } else {
            post.style.display = 'none';
        }
    });
}

function initFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterPosts, 300));
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterPosts);
    }
}

// ============================================
// FONCTIONNALITÉ TEXT-TO-SPEECH (Synthèse vocale)
// ============================================

let currentUtterance = null;
let isSpeaking = false;
let voicesLoaded = false;

// Charger les voix disponibles
function loadVoices() {
    if ('speechSynthesis' in window) {
        const voices = window.speechSynthesis.getVoices();
        if (voices.length > 0) {
            voicesLoaded = true;
            console.log('Voix chargées:', voices.length);
            return voices;
        }
    }
    return [];
}

// Initialiser le chargement des voix
if ('speechSynthesis' in window) {
    // Certains navigateurs chargent les voix de manière asynchrone
    window.speechSynthesis.onvoiceschanged = function() {
        voicesLoaded = true;
        console.log('Voix disponibles:', window.speechSynthesis.getVoices().length);
    };
    
    // Essayer de charger immédiatement
    loadVoices();
}

/**
 * Trouve une voix française ou utilise la voix par défaut
 */
function getVoice(lang = 'fr-FR') {
    const voices = window.speechSynthesis.getVoices();
    
    // Chercher une voix française
    let frenchVoice = voices.find(voice => 
        voice.lang.startsWith('fr') || 
        voice.lang === 'fr-FR' || 
        voice.name.toLowerCase().includes('french')
    );
    
    if (frenchVoice) {
        console.log('Voix française trouvée:', frenchVoice.name);
        return frenchVoice;
    }
    
    // Si pas de voix française, utiliser la voix par défaut
    const defaultVoice = voices.find(voice => voice.default) || voices[0];
    if (defaultVoice) {
        console.log('Utilisation de la voix par défaut:', defaultVoice.name);
    }
    
    return defaultVoice;
}

/**
 * Lit un texte à voix haute en utilisant l'API Web Speech Synthesis
 * @param {string} text - Le texte à lire
 * @param {string} lang - La langue (par défaut: 'fr-FR')
 */
function speakText(text, lang = 'fr-FR') {
    // Arrêter la lecture en cours si elle existe
    if (isSpeaking && currentUtterance) {
        window.speechSynthesis.cancel();
        isSpeaking = false;
        currentUtterance = null;
        updateSpeechButtonState(false);
        console.log('Lecture arrêtée');
        return;
    }
    
    // Vérifier si l'API est supportée
    if (!('speechSynthesis' in window)) {
        alert('Désolé, la synthèse vocale n\'est pas supportée par votre navigateur.');
        console.error('Speech Synthesis API non supportée');
        return;
    }
    
    // Nettoyer le texte (supprimer les balises HTML, les emojis, etc.)
    let cleanText = text.replace(/<[^>]*>/g, '').replace(/[🔊🔇]/g, '').trim();
    // Supprimer les sauts de ligne multiples
    cleanText = cleanText.replace(/\n+/g, '. ');
    
    if (!cleanText) {
        alert('Aucun texte à lire.');
        console.error('Texte vide après nettoyage');
        return;
    }
    
    console.log('=== DÉBUT DE LA LECTURE ===');
    console.log('Texte à lire:', cleanText.substring(0, 100) + '...');
    console.log('Longueur:', cleanText.length);
    
    // Fonction pour lancer la lecture
    function startSpeaking() {
        // Créer l'énoncé
        currentUtterance = new SpeechSynthesisUtterance(cleanText);
        currentUtterance.lang = lang;
        currentUtterance.rate = 1.0; // Vitesse de lecture (0.1 à 10)
        currentUtterance.pitch = 1.0; // Hauteur de la voix (0 à 2)
        currentUtterance.volume = 1.0; // Volume (0 à 1)
        
        // Essayer de trouver une voix française
        const voices = window.speechSynthesis.getVoices();
        console.log('Voix disponibles au moment de la lecture:', voices.length);
        
        if (voices.length > 0) {
            const voice = getVoice(lang);
            if (voice) {
                currentUtterance.voice = voice;
                console.log('Voix sélectionnée:', voice.name, voice.lang);
            }
        } else {
            console.warn('Aucune voix disponible, utilisation de la voix par défaut');
        }
        
        // Gérer les événements
        currentUtterance.onstart = function() {
            isSpeaking = true;
            updateSpeechButtonState(true);
            console.log('✅ Lecture démarrée avec succès');
        };
        
        currentUtterance.onend = function() {
            isSpeaking = false;
            currentUtterance = null;
            updateSpeechButtonState(false);
            console.log('✅ Lecture terminée');
        };
        
        currentUtterance.onerror = function(event) {
            console.error('❌ Erreur lors de la synthèse vocale:', event);
            console.error('Type d\'erreur:', event.error);
            isSpeaking = false;
            currentUtterance = null;
            updateSpeechButtonState(false);
            
            let errorMsg = 'Erreur lors de la lecture. ';
            if (event.error === 'not-allowed') {
                errorMsg += 'Veuillez autoriser l\'accès au microphone/son dans les paramètres de votre navigateur.';
            } else if (event.error === 'network') {
                errorMsg += 'Problème de connexion réseau.';
            } else {
                errorMsg += 'Veuillez réessayer. Code: ' + event.error;
            }
            alert(errorMsg);
        };
        
        // Lancer la lecture
        try {
            window.speechSynthesis.speak(currentUtterance);
            console.log('✅ Commande speak() envoyée avec succès');
        } catch (error) {
            console.error('❌ Erreur lors de l\'appel à speak:', error);
            alert('Erreur lors du démarrage de la lecture: ' + error.message);
        }
    }
    
    // S'assurer que les voix sont chargées avant de parler
    const voicesCheck = window.speechSynthesis.getVoices();
    console.log('Voix disponibles au démarrage:', voicesCheck.length);
    
    if (voicesCheck.length === 0) {
        console.log('Attente du chargement des voix...');
        const voicesHandler = function() {
            console.log('Voix chargées, démarrage de la lecture...');
            window.speechSynthesis.removeEventListener('voiceschanged', voicesHandler);
            startSpeaking();
        };
        window.speechSynthesis.addEventListener('voiceschanged', voicesHandler);
        
        // Forcer le rechargement des voix
        window.speechSynthesis.getVoices();
    } else {
        // Les voix sont déjà chargées, lancer directement
        startSpeaking();
    }
}

/**
 * Lit une publication complète (titre + contenu)
 * @param {string} titre - Le titre de la publication
 * @param {string} contenu - Le contenu de la publication
 */
function speakPublication(titre, contenu) {
    const fullText = titre + '. ' + contenu;
    speakText(fullText);
}

/**
 * Met à jour l'état visuel du bouton de lecture
 * @param {boolean} speaking - True si en cours de lecture
 */
function updateSpeechButtonState(speaking) {
    const buttons = document.querySelectorAll('.speech-btn');
    buttons.forEach(function(btn) {
        if (speaking) {
            btn.classList.add('speaking');
            btn.innerHTML = '🔇';
            btn.title = 'Arrêter la lecture';
        } else {
            btn.classList.remove('speaking');
            btn.innerHTML = '🔊';
            btn.title = 'Écouter la publication';
        }
    });
}

/**
 * Initialise les boutons de synthèse vocale
 */
function initSpeechButtons() {
    const speechButtons = document.querySelectorAll('.speech-btn');
    console.log('Initialisation de', speechButtons.length, 'boutons de synthèse vocale');
    
    if (speechButtons.length === 0) {
        console.warn('Aucun bouton de synthèse vocale trouvé');
        return;
    }
    
    speechButtons.forEach(function(btn, index) {
        // S'assurer que le bouton est cliquable
        btn.style.pointerEvents = 'auto';
        btn.style.cursor = 'pointer';
        
        // Ajouter l'event listener directement (sans cloner)
        btn.addEventListener('click', function handleSpeechClick(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            console.log('=== CLIC SUR BOUTON SYNTHÈSE VOCALE ===', index);
            
            // Récupérer le texte à lire depuis les attributs data
            let titre = this.getAttribute('data-titre') || '';
            let contenu = this.getAttribute('data-contenu') || '';
            
            console.log('Titre (brut):', titre.substring(0, 50));
            console.log('Contenu (brut):', contenu.substring(0, 50));
            
            // Décoder les entités HTML
            function decodeHtml(html) {
                const txt = document.createElement('textarea');
                txt.innerHTML = html;
                return txt.value;
            }
            
            if (titre) {
                titre = decodeHtml(titre);
            }
            if (contenu) {
                contenu = decodeHtml(contenu);
            }
            
            console.log('Titre (décodé):', titre.substring(0, 50));
            console.log('Contenu (décodé):', contenu.substring(0, 50));
            
            if (titre || contenu) {
                const fullText = (titre ? titre + '. ' : '') + contenu;
                console.log('Texte complet à lire:', fullText.substring(0, 100));
                console.log('Longueur du texte:', fullText.length);
                
                if (fullText.trim().length > 0) {
                    speakText(fullText);
                } else {
                    console.error('Texte vide après traitement');
                    alert('Erreur: Le texte est vide.');
                }
            } else {
                // Si pas d'attributs data, chercher le texte dans le parent
                console.log('Pas de données dans les attributs, recherche dans le DOM...');
                const card = this.closest('.card');
                if (card) {
                    const titreElement = card.querySelector('h2, h3');
                    const contenuElement = card.querySelector('p');
                    const titreText = titreElement ? titreElement.textContent.trim() : '';
                    const contenuText = contenuElement ? contenuElement.textContent.trim() : '';
                    const fullText = (titreText ? titreText + '. ' : '') + contenuText;
                    console.log('Texte trouvé dans le DOM:', fullText.substring(0, 100));
                    
                    if (fullText.trim().length > 0) {
                        speakText(fullText);
                    } else {
                        console.error('Texte vide dans le DOM');
                        alert('Impossible de trouver le texte à lire.');
                    }
                } else {
                    console.error('Impossible de trouver la carte parente');
                    alert('Impossible de trouver le contenu à lire.');
                }
            }
        }, { once: false, capture: false });
        
        // Ajouter aussi un test au survol
        newBtn.addEventListener('mouseenter', function() {
            console.log('Souris sur le bouton', index);
        });
    });
    
    console.log('Tous les boutons de synthèse vocale ont été initialisés');
}

// Initialiser les boutons de synthèse vocale au chargement
// (L'initialisation est déjà faite au début du fichier)

