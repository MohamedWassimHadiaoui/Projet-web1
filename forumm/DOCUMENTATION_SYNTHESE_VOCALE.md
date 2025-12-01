# 📄 Documentation - Fonctionnalité de Synthèse Vocale (Text-to-Speech)

## 📋 Informations du Projet

**Projet :** Forum MVC - PeaceConnect  
**Fonctionnalité :** Synthèse vocale pour les publications  
**Date :** 2024  
**Technologie :** Web Speech API (Speech Synthesis)

---

## 🎯 Objectif

Implémenter une fonctionnalité de synthèse vocale permettant de lire à voix haute le contenu des publications du forum, similaire à la fonctionnalité disponible dans Google Traduction. Chaque publication dispose d'une icône 🔊 qui, lorsqu'on clique dessus, lit automatiquement le titre et le contenu de la publication.

---

## 💻 Technologies Utilisées

### API Web Speech Synthesis

L'API Web Speech Synthesis est une API native du navigateur qui permet de convertir du texte en parole sans nécessiter de serveur externe ou d'API tierce.

**Avantages :**
- ✅ Gratuite et intégrée au navigateur
- ✅ Fonctionne hors ligne
- ✅ Pas de dépendance externe
- ✅ Supporte plusieurs langues
- ✅ Facile à implémenter
- ✅ Performante (pas de latence réseau)

**Compatibilité navigateurs :**
- Chrome/Edge : ✅ Support complet
- Firefox : ✅ Support complet
- Safari : ✅ Support complet
- Opera : ✅ Support complet

---

## 📁 Structure de l'Implémentation

### Fichiers Modifiés

1. **`views/forum.php`**
   - Ajout de l'icône 🔊 à côté de chaque publication
   - Intégration du script JavaScript pour la synthèse vocale

2. **`views/view_publication.php`**
   - Ajout de l'icône 🔊 à côté du titre de la publication
   - Intégration du script JavaScript pour la synthèse vocale

3. **`views/assets/css/components.css`**
   - Styles CSS pour le bouton de synthèse vocale
   - Animations pendant la lecture

4. **`views/test_speech.php`** (optionnel)
   - Page de test et diagnostic de l'API

---

## 🔧 Implémentation Détaillée

### 1. Interface Utilisateur (HTML)

#### Dans `views/forum.php`

```php
<button type="button" 
        class="speech-btn" 
        title="Écouter la publication"
        data-titre="<?php echo htmlspecialchars($pub->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"
        data-contenu="<?php echo htmlspecialchars($pub->getContenu(), ENT_QUOTES, 'UTF-8'); ?>"
        onclick="speakPublicationFromButton(this); return false;">
    🔊
</button>
```

**Explication :**
- `type="button"` : Définit un bouton (pas un submit de formulaire)
- `class="speech-btn"` : Classe CSS pour le style
- `data-titre` et `data-contenu` : Attributs HTML5 pour stocker les données (plus sûr que de passer dans onclick)
- `onclick` : Appelle la fonction JavaScript au clic
- `htmlspecialchars()` : Sécurise les données pour éviter les injections XSS

#### Dans `views/view_publication.php`

Même structure pour la page de détail d'une publication.

---

### 2. JavaScript - Fonctionnalité Principale

#### Fonction principale dans `views/forum.php` (avant `</body>`)

```javascript
// Variables globales pour gérer l'état de la lecture
let isSpeaking = false;
let currentUtterance = null;

// Fonction intermédiaire pour récupérer les données depuis les attributs data
function speakPublicationFromButton(button) {
    const titre = button.getAttribute('data-titre') || '';
    const contenu = button.getAttribute('data-contenu') || '';
    
    console.log('=== FONCTION speakPublicationFromButton APPELÉE ===');
    console.log('Titre (longueur):', titre ? titre.length : 0);
    console.log('Contenu (longueur):', contenu ? contenu.length : 0);
    
    speakPublicationSimple(button, titre, contenu);
}

// Fonction principale de synthèse vocale
function speakPublicationSimple(button, titre, contenu) {
    // 1. Arrêter si déjà en cours de lecture
    if (isSpeaking && currentUtterance) {
        window.speechSynthesis.cancel();
        isSpeaking = false;
        currentUtterance = null;
        button.innerHTML = '🔊';
        button.classList.remove('speaking');
        return;
    }
    
    // 2. Vérifier que l'API est supportée
    if (!('speechSynthesis' in window)) {
        alert('Synthèse vocale non supportée par votre navigateur');
        return;
    }
    
    // 3. Nettoyer et préparer le texte
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
    // Supprimer les espaces multiples
    cleanContenu = cleanContenu.replace(/\s+/g, ' ');
    
    // 4. Préparer le texte complet
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
    
    // 5. Créer l'énoncé (SpeechSynthesisUtterance)
    currentUtterance = new SpeechSynthesisUtterance(fullText);
    currentUtterance.lang = 'fr-FR';        // Langue française
    currentUtterance.rate = 1.0;            // Vitesse (0.1 à 10)
    currentUtterance.pitch = 1.0;           // Hauteur (0 à 2)
    currentUtterance.volume = 1.0;          // Volume (0 à 1)
    
    // 6. Trouver une voix française
    const voices = window.speechSynthesis.getVoices();
    if (voices.length > 0) {
        const frenchVoice = voices.find(v => v.lang.startsWith('fr')) || voices[0];
        currentUtterance.voice = frenchVoice;
        console.log('Voix utilisée:', frenchVoice.name);
    }
    
    // 7. Gérer les événements
    currentUtterance.onstart = function() {
        isSpeaking = true;
        button.innerHTML = '🔇';  // Change l'icône pendant la lecture
        button.classList.add('speaking');
        console.log('✅ Lecture démarrée');
    };
    
    currentUtterance.onend = function() {
        isSpeaking = false;
        currentUtterance = null;
        button.innerHTML = '🔊';  // Remet l'icône normale
        button.classList.remove('speaking');
        console.log('✅ Lecture terminée');
    };
    
    currentUtterance.onerror = function(event) {
        console.error('❌ Erreur:', event.error);
        isSpeaking = false;
        currentUtterance = null;
        button.innerHTML = '🔊';
        button.classList.remove('speaking');
        alert('Erreur lors de la lecture: ' + event.error);
    };
    
    // 8. Lancer la lecture
    try {
        window.speechSynthesis.speak(currentUtterance);
        console.log('✅ Commande speak() envoyée');
    } catch (error) {
        console.error('❌ Erreur speak():', error);
        alert('Erreur: ' + error.message);
    }
}

// Attendre que les voix se chargent
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = function() {
        console.log('Voix chargées:', window.speechSynthesis.getVoices().length);
    };
}
```

**Explication étape par étape :**

1. **Vérification de l'état** : Si une lecture est en cours, on l'arrête
2. **Vérification de l'API** : On s'assure que le navigateur supporte l'API
3. **Nettoyage du texte** : 
   - Décodage des entités HTML (`&amp;` → `&`)
   - Suppression des balises HTML
   - Remplacement des sauts de ligne par des points
   - Suppression des espaces multiples
4. **Création de l'énoncé** : On crée un objet `SpeechSynthesisUtterance` avec le texte
5. **Configuration** : On définit la langue (fr-FR), la vitesse, la hauteur et le volume
6. **Sélection de la voix** : On cherche une voix française, sinon on utilise la voix par défaut
7. **Gestion des événements** : On gère le début, la fin et les erreurs
8. **Lancement** : On appelle `speak()` pour lancer la lecture

---

### 3. Styles CSS

#### Dans `views/assets/css/components.css`

```css
/* Bouton de synthèse vocale */
.speech-btn {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 40px !important;
    height: 40px !important;
    padding: 0 !important;
    margin: 0 !important;
    margin-left: 0.5rem !important;
    border: 2px solid var(--color-primary) !important;
    background-color: var(--color-white) !important;
    color: var(--color-primary) !important;
    border-radius: 50% !important;
    cursor: pointer !important;
    font-size: 1.2rem !important;
    transition: all var(--transition-fast);
    flex-shrink: 0;
    position: relative;
    z-index: 10;
    outline: none;
}

/* Effet au survol */
.speech-btn:hover {
    background-color: var(--color-primary) !important;
    color: var(--color-white) !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* État pendant la lecture */
.speech-btn.speaking {
    background-color: var(--color-error) !important;
    border-color: var(--color-error) !important;
    color: var(--color-white) !important;
    animation: pulse 1.5s ease-in-out infinite;
}

/* Animation pulse pendant la lecture */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 8px rgba(220, 38, 38, 0);
    }
}

/* Effet au clic */
.speech-btn:active {
    transform: scale(0.95);
}
```

**Caractéristiques du style :**
- Bouton circulaire (border-radius: 50%)
- Changement de couleur au survol (bleu → blanc)
- Animation pulse rouge pendant la lecture
- Effet de scale au clic
- Utilisation de `!important` pour éviter les conflits CSS

---

## ⚙️ Fonctionnement Détaillé

### Flux d'exécution complet

```
1. Utilisateur clique sur l'icône 🔊
   ↓
2. Fonction speakPublicationFromButton() appelée
   ↓
3. Récupération des données depuis data-titre et data-contenu
   ↓
4. Fonction speakPublicationSimple() appelée
   ↓
5. Vérification si lecture en cours
   - Si oui → Arrête la lecture
   - Si non → Continue
   ↓
6. Vérification de l'API Speech Synthesis
   - Si non supportée → Affiche une alerte
   - Si supportée → Continue
   ↓
7. Nettoyage du texte
   - Décodage HTML
   - Suppression des balises
   - Remplacement des sauts de ligne
   ↓
8. Préparation du texte complet
   - Combine titre + contenu
   - Format: "Titre. Contenu"
   ↓
9. Création de SpeechSynthesisUtterance
   - Définit la langue (fr-FR)
   - Définit la vitesse, hauteur, volume
   - Sélectionne une voix française
   ↓
10. Configuration des événements
    - onstart: Change l'icône en 🔇 et ajoute l'animation
    - onend: Remet l'icône en 🔊
    - onerror: Gère les erreurs
    ↓
11. Lancement de la lecture
    - window.speechSynthesis.speak(currentUtterance)
    ↓
12. Lecture à voix haute 🔊
```

---

## 📊 Paramètres de l'API

### SpeechSynthesisUtterance

| Paramètre | Type | Valeur par défaut | Description |
|-----------|------|-------------------|-------------|
| `text` | string | - | Le texte à lire |
| `lang` | string | 'fr-FR' | Langue de la synthèse vocale |
| `rate` | number | 1.0 | Vitesse de lecture (0.1 à 10) |
| `pitch` | number | 1.0 | Hauteur de la voix (0 à 2) |
| `volume` | number | 1.0 | Volume (0 à 1) |
| `voice` | object | null | Voix spécifique à utiliser |

### Méthodes de l'API SpeechSynthesis

| Méthode | Description |
|---------|-------------|
| `speak(utterance)` | Lance la lecture d'un énoncé |
| `cancel()` | Arrête toutes les lectures en cours |
| `pause()` | Met en pause la lecture |
| `resume()` | Reprend la lecture en pause |
| `getVoices()` | Retourne la liste des voix disponibles |

### Événements de SpeechSynthesisUtterance

| Événement | Description |
|-----------|-------------|
| `onstart` | Déclenché quand la lecture commence |
| `onend` | Déclenché quand la lecture se termine |
| `onerror` | Déclenché en cas d'erreur |
| `onpause` | Déclenché quand la lecture est mise en pause |
| `onresume` | Déclenché quand la lecture reprend |

---

## 🔍 Sécurité et Bonnes Pratiques

### 1. Échappement des données PHP

```php
// ✅ Correct
data-titre="<?php echo htmlspecialchars($pub->getTitre(), ENT_QUOTES, 'UTF-8'); ?>"

// ❌ Incorrect (vulnérable aux injections XSS)
data-titre="<?php echo $pub->getTitre(); ?>"
```

**Pourquoi ?**
- `htmlspecialchars()` convertit les caractères spéciaux en entités HTML
- `ENT_QUOTES` échappe aussi les guillemets simples et doubles
- `UTF-8` garantit l'encodage correct

### 2. Utilisation des attributs data-*

```html
<!-- ✅ Correct : Utilisation de data-* -->
<button data-titre="..." data-contenu="..." onclick="speakPublicationFromButton(this)">

<!-- ❌ Incorrect : Passage direct dans onclick -->
<button onclick="speak('<?php echo $titre; ?>')">
```

**Avantages :**
- Évite les problèmes d'échappement
- Plus maintenable
- Plus sécurisé

### 3. Gestion des erreurs

```javascript
currentUtterance.onerror = function(event) {
    console.error('Erreur:', event.error);
    // Réinitialiser l'état
    isSpeaking = false;
    currentUtterance = null;
    // Informer l'utilisateur
    alert('Erreur lors de la lecture');
};
```

---

## 🧪 Tests et Validation

### Test 1 : Vérification de l'API

```javascript
// Dans la console du navigateur (F12)
if ('speechSynthesis' in window) {
    console.log('✅ API disponible');
} else {
    console.log('❌ API non disponible');
}
```

### Test 2 : Test de lecture simple

```javascript
// Dans la console
var test = new SpeechSynthesisUtterance('Test de synthèse vocale');
window.speechSynthesis.speak(test);
```

**Résultat attendu :** Vous devriez entendre "Test de synthèse vocale"

### Test 3 : Liste des voix disponibles

```javascript
// Dans la console
const voices = window.speechSynthesis.getVoices();
console.log('Voix disponibles:', voices.length);
voices.forEach(v => console.log(v.name, v.lang));
```

### Test 4 : Test complet sur le site

1. Ouvrir : `http://localhost/forumm/views/forum.php`
2. Ouvrir la console (F12)
3. Cliquer sur l'icône 🔊
4. Vérifier dans la console :
   - "=== FONCTION speakPublicationFromButton APPELÉE ==="
   - "Titre (longueur): X"
   - "Contenu (longueur): Y"
   - "✅ Commande speak() envoyée"
   - "✅ Lecture démarrée"
5. Vérifier que le son est audible
6. Vérifier que l'icône change en 🔇 pendant la lecture

---

## 🐛 Résolution des Problèmes

### Problème 1 : Pas de son

**Causes possibles :**
- Volume système à zéro
- Volume du navigateur à zéro
- API non supportée par le navigateur

**Solutions :**
1. Vérifier le volume Windows
2. Vérifier le volume du navigateur
3. Tester dans Chrome/Edge (meilleur support)
4. Tester directement dans la console :
   ```javascript
   var test = new SpeechSynthesisUtterance('Test');
   window.speechSynthesis.speak(test);
   ```

### Problème 2 : Seul le titre est lu

**Cause :** Le contenu n'est pas correctement récupéré ou nettoyé

**Solution :**
- Vérifier dans la console la longueur du contenu
- Vérifier que `data-contenu` contient bien le texte
- Vérifier le nettoyage du texte (suppression des balises HTML)

### Problème 3 : Erreurs JavaScript

**Causes possibles :**
- Guillemets non échappés dans le PHP
- Caractères spéciaux non gérés

**Solutions :**
- Utiliser `htmlspecialchars()` avec `ENT_QUOTES`
- Utiliser les attributs `data-*` au lieu de passer directement dans `onclick`
- Vérifier la console pour les erreurs exactes

### Problème 4 : Voix non française

**Cause :** Aucune voix française disponible sur le système

**Solution :**
- Le code utilise automatiquement la voix par défaut si aucune voix française n'est trouvée
- Les voix dépendent du système d'exploitation
- Windows : Voix françaises généralement disponibles
- Linux : Peut nécessiter l'installation de paquets supplémentaires

---

## 📈 Améliorations Possibles

### 1. Sélection de la langue
Permettre à l'utilisateur de choisir la langue de lecture.

### 2. Contrôles de lecture
Ajouter des boutons pour :
- Pause/Reprendre
- Vitesse ajustable (slider)
- Volume ajustable

### 3. Voix personnalisée
Permettre à l'utilisateur de choisir la voix parmi celles disponibles.

### 4. Lecture par sections
Lire le contenu section par section (paragraphe par paragraphe).

### 5. Sauvegarde des préférences
Sauvegarder les préférences (langue, vitesse, voix) dans `localStorage`.

### 6. Indicateur de progression
Afficher une barre de progression pendant la lecture.

---

## 📝 Conclusion

Cette implémentation de la synthèse vocale utilise l'API Web Speech Synthesis native du navigateur pour offrir une fonctionnalité similaire à Google Traduction. La solution est :

- ✅ **Simple** : Pas de dépendance externe
- ✅ **Performante** : Fonctionne côté client
- ✅ **Gratuite** : Pas de coût d'API
- ✅ **Accessible** : Améliore l'accessibilité du site
- ✅ **Compatible** : Fonctionne sur tous les navigateurs modernes

### Points clés de l'implémentation

1. **Sécurité** : Utilisation de `htmlspecialchars()` et attributs `data-*`
2. **Robustesse** : Gestion complète des erreurs
3. **UX** : Feedback visuel (changement d'icône, animation)
4. **Performance** : Pas de requêtes serveur, tout se fait côté client
5. **Maintenabilité** : Code bien structuré et commenté

---

## 📚 Ressources

- [MDN - Web Speech API](https://developer.mozilla.org/fr/docs/Web/API/Web_Speech_API)
- [MDN - SpeechSynthesis](https://developer.mozilla.org/fr/docs/Web/API/SpeechSynthesis)
- [MDN - SpeechSynthesisUtterance](https://developer.mozilla.org/fr/docs/Web/API/SpeechSynthesisUtterance)
- [Can I Use - Speech Synthesis](https://caniuse.com/speech-synthesis)

---

**Document créé le :** 2024  
**Version :** 1.0  
**Auteur :** Implémentation pour le projet Forum MVC - PeaceConnect


