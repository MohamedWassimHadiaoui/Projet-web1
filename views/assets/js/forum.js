document.addEventListener('DOMContentLoaded', function() {
    initCharCounters();
    initFilters();
    initSpecialCharsValidation();
    
    if ('speechSynthesis' in window) {
        setTimeout(function() {
            const voices = window.speechSynthesis.getVoices();
            if (voices.length === 0) {
            }
        }, 1000);
    } else {
        const speechButtons = document.querySelectorAll('.speech-btn');
        speechButtons.forEach(function(btn) {
            btn.disabled = true;
            btn.title = 'Synthèse vocale non supportée';
            btn.style.opacity = '0.5';
        });
    }
});

function initSpecialCharsValidation() {
    const titreField = document.getElementById('titre');
    const auteurField = document.getElementById('auteur');
    
    function validateSpecialChars(event) {
        const field = event.target;
        const fieldId = field.id;
        validateField(fieldId);
    }
    
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
    let isValid = true;
    
    const titreField = document.getElementById('titre');
    const auteurField = document.getElementById('auteur');
    const categorieField = document.getElementById('categorie');
    const contenuField = document.getElementById('contenu');
    
    if (typeof validateNoSpecialChars === 'undefined') {
        alert('Erreur de validation. Veuillez recharger la page.');
        return false;
    }
    
    if (typeof validateOnlyLetters === 'undefined') {
        alert('Erreur de validation. Veuillez recharger la page.');
        return false;
    }
    
    if (titreField) {
        const titreValue = titreField.value.trim();
        
        if (!titreValue) {
            isValid = false;
            validateField('titre');
        } else {
            const titreLettersValidation = validateOnlyLetters(titreValue);
            if (!titreLettersValidation.valid) {
                isValid = false;
                validateField('titre');
            } else if (!validateField('titre')) {
                isValid = false;
            }
        }
    }
    
    if (auteurField) {
        const auteurValue = auteurField.value.trim();
        
        if (!auteurValue) {
            isValid = false;
            validateField('auteur');
        } else {
            const auteurLettersValidation = validateOnlyLetters(auteurValue);
            if (!auteurLettersValidation.valid) {
                isValid = false;
                validateField('auteur');
            } else if (!validateField('auteur')) {
                isValid = false;
            }
        }
    }
    
    if (!validateField('categorie')) {
        isValid = false;
    }
    
    if (!validateField('contenu')) {
        isValid = false;
    }
    
    if (!isValid) {
        alert('Veuillez corriger les erreurs dans le formulaire. Les caractères spéciaux ne sont pas autorisés dans le nom et le titre.');
        return false;
    }
    
    return true;
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

let currentUtterance = null;
let isSpeaking = false;
let voicesLoaded = false;

function loadVoices() {
    if ('speechSynthesis' in window) {
        const voices = window.speechSynthesis.getVoices();
        if (voices.length > 0) {
            voicesLoaded = true;
            return voices;
        }
    }
    return [];
}

if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = function() {
        voicesLoaded = true;
    };
    loadVoices();
}

function getVoice(lang = 'fr-FR') {
    const voices = window.speechSynthesis.getVoices();
    
    let frenchVoice = voices.find(voice => 
        voice.lang.startsWith('fr') || 
        voice.lang === 'fr-FR' || 
        voice.name.toLowerCase().includes('french')
    );
    
    if (frenchVoice) {
        return frenchVoice;
    }
    
    const defaultVoice = voices.find(voice => voice.default) || voices[0];
    return defaultVoice;
}

function speakText(text, lang = 'fr-FR') {
    if (isSpeaking && currentUtterance) {
        window.speechSynthesis.cancel();
        isSpeaking = false;
        currentUtterance = null;
        updateSpeechButtonState(false);
        return;
    }
    
    if (!('speechSynthesis' in window)) {
        alert('Désolé, la synthèse vocale n\'est pas supportée par votre navigateur.');
        return;
    }
    
    let cleanText = text.replace(/<[^>]*>/g, '').replace(/[🔊🔇]/g, '').trim();
    cleanText = cleanText.replace(/\n+/g, '. ');
    
    if (!cleanText) {
        alert('Aucun texte à lire.');
        return;
    }
    
    function startSpeaking() {
        currentUtterance = new SpeechSynthesisUtterance(cleanText);
        currentUtterance.lang = lang;
        currentUtterance.rate = 1.0;
        currentUtterance.pitch = 1.0;
        currentUtterance.volume = 1.0;
        
        const voices = window.speechSynthesis.getVoices();
        
        if (voices.length > 0) {
            const voice = getVoice(lang);
            if (voice) {
                currentUtterance.voice = voice;
            }
        }
        
        currentUtterance.onstart = function() {
            isSpeaking = true;
            updateSpeechButtonState(true);
        };
        
        currentUtterance.onend = function() {
            isSpeaking = false;
            currentUtterance = null;
            updateSpeechButtonState(false);
        };
        
        currentUtterance.onerror = function(event) {
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
        
        try {
            window.speechSynthesis.speak(currentUtterance);
        } catch (error) {
            alert('Erreur lors du démarrage de la lecture: ' + error.message);
        }
    }
    
    const voicesCheck = window.speechSynthesis.getVoices();
    
    if (voicesCheck.length === 0) {
        const voicesHandler = function() {
            window.speechSynthesis.removeEventListener('voiceschanged', voicesHandler);
            startSpeaking();
        };
        window.speechSynthesis.addEventListener('voiceschanged', voicesHandler);
        window.speechSynthesis.getVoices();
    } else {
        startSpeaking();
    }
}

function speakPublication(titre, contenu) {
    const fullText = titre + '. ' + contenu;
    speakText(fullText);
}

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

function initSpeechButtons() {
    const speechButtons = document.querySelectorAll('.speech-btn');
    
    if (speechButtons.length === 0) {
        return;
    }
    
    speechButtons.forEach(function(btn, index) {
        btn.style.pointerEvents = 'auto';
        btn.style.cursor = 'pointer';
        
        btn.addEventListener('click', function handleSpeechClick(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            let titre = this.getAttribute('data-titre') || '';
            let contenu = this.getAttribute('data-contenu') || '';
            
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
            
            if (titre || contenu) {
                const fullText = (titre ? titre + '. ' : '') + contenu;
                
                if (fullText.trim().length > 0) {
                    speakText(fullText);
                } else {
                    alert('Erreur: Le texte est vide.');
                }
            } else {
                const card = this.closest('.card');
                if (card) {
                    const titreElement = card.querySelector('h2, h3');
                    const contenuElement = card.querySelector('p');
                    const titreText = titreElement ? titreElement.textContent.trim() : '';
                    const contenuText = contenuElement ? contenuElement.textContent.trim() : '';
                    const fullText = (titreText ? titreText + '. ' : '') + contenuText;
                    
                    if (fullText.trim().length > 0) {
                        speakText(fullText);
                    } else {
                        alert('Impossible de trouver le texte à lire.');
                    }
                } else {
                    alert('Impossible de trouver le contenu à lire.');
                }
            }
        }, { once: false, capture: false });
    });
}
