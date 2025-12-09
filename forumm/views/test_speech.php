<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Synthèse Vocale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 5px;
        }
        button:hover {
            background: #0056b3;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .status {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
        }
        #log {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>🔊 Test de la Synthèse Vocale</h1>
    
    <div class="test-section">
        <h2>1. Vérification de l'API</h2>
        <div id="apiStatus" class="status"></div>
        <button onclick="checkAPI()">Vérifier l'API</button>
    </div>
    
    <div class="test-section">
        <h2>2. Liste des voix disponibles</h2>
        <div id="voicesList"></div>
        <button onclick="listVoices()">Lister les voix</button>
    </div>
    
    <div class="test-section">
        <h2>3. Test de lecture simple</h2>
        <button onclick="testSimple()">Lire "Bonjour, ceci est un test"</button>
        <button onclick="testFrench()">Lire "Bonjour, je suis une voix française"</button>
        <button onclick="stopSpeech()">Arrêter</button>
    </div>
    
    <div class="test-section">
        <h2>4. Test avec texte personnalisé</h2>
        <textarea id="customText" rows="4" style="width: 100%; padding: 10px; font-size: 14px;">Entrez votre texte ici pour le tester...</textarea>
        <br>
        <button onclick="testCustom()">Lire le texte ci-dessus</button>
    </div>
    
    <div class="test-section">
        <h2>5. Logs de débogage</h2>
        <div id="log"></div>
        <button onclick="clearLog()">Effacer les logs</button>
    </div>
    
    <script>
        let synth = window.speechSynthesis;
        let currentUtterance = null;
        
        function log(message, type = 'info') {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            const color = type === 'error' ? '#f00' : type === 'success' ? '#0f0' : '#0ff';
            logDiv.innerHTML += `<div style="color: ${color}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(message);
        }
        
        function clearLog() {
            document.getElementById('log').innerHTML = '';
        }
        
        function checkAPI() {
            const statusDiv = document.getElementById('apiStatus');
            if ('speechSynthesis' in window) {
                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✅ API Speech Synthesis disponible !';
                log('API Speech Synthesis disponible', 'success');
            } else {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '❌ API Speech Synthesis NON disponible dans ce navigateur';
                log('API Speech Synthesis NON disponible', 'error');
            }
        }
        
        function listVoices() {
            const voicesListDiv = document.getElementById('voicesList');
            const voices = synth.getVoices();
            
            if (voices.length === 0) {
                voicesListDiv.innerHTML = '<div class="status error">Aucune voix disponible. Attendez quelques secondes et réessayez.</div>';
                log('Aucune voix disponible', 'error');
                
                synth.onvoiceschanged = function() {
                    listVoices();
                };
                return;
            }
            
            let html = '<div class="status info">Nombre de voix: ' + voices.length + '</div><ul style="list-style: none; padding: 0;">';
            voices.forEach(function(voice, index) {
                const isFrench = voice.lang.startsWith('fr');
                const isDefault = voice.default;
                html += `<li style="padding: 5px; ${isFrench ? 'background: #d4edda;' : ''}">
                    ${isDefault ? '⭐ ' : ''}${isFrench ? '🇫🇷 ' : ''}
                    <strong>${voice.name}</strong> - ${voice.lang} 
                    ${voice.localService ? '(Locale)' : '(Réseau)'}
                </li>`;
            });
            html += '</ul>';
            voicesListDiv.innerHTML = html;
            
            log('Voix listées: ' + voices.length, 'success');
            voices.forEach(function(voice) {
                if (voice.lang.startsWith('fr')) {
                    log('Voix française trouvée: ' + voice.name + ' (' + voice.lang + ')', 'success');
                }
            });
        }
        
        function testSimple() {
            log('Démarrage du test simple...', 'info');
            stopSpeech();
            
            const utterance = new SpeechSynthesisUtterance('Bonjour, ceci est un test de synthèse vocale');
            utterance.lang = 'fr-FR';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            
            utterance.onstart = function() {
                log('✅ Lecture démarrée', 'success');
            };
            
            utterance.onend = function() {
                log('✅ Lecture terminée', 'success');
            };
            
            utterance.onerror = function(event) {
                log('❌ Erreur: ' + event.error, 'error');
                alert('Erreur: ' + event.error);
            };
            
            currentUtterance = utterance;
            synth.speak(utterance);
            log('Commande speak() envoyée', 'info');
        }
        
        function testFrench() {
            log('Démarrage du test français...', 'info');
            stopSpeech();
            
            const voices = synth.getVoices();
            let frenchVoice = voices.find(v => v.lang.startsWith('fr'));
            
            const utterance = new SpeechSynthesisUtterance('Bonjour, je suis une voix française');
            utterance.lang = 'fr-FR';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            
            if (frenchVoice) {
                utterance.voice = frenchVoice;
                log('Utilisation de la voix: ' + frenchVoice.name, 'success');
            } else {
                log('Aucune voix française trouvée, utilisation de la voix par défaut', 'info');
            }
            
            utterance.onstart = function() {
                log('✅ Lecture démarrée', 'success');
            };
            
            utterance.onend = function() {
                log('✅ Lecture terminée', 'success');
            };
            
            utterance.onerror = function(event) {
                log('❌ Erreur: ' + event.error, 'error');
                alert('Erreur: ' + event.error);
            };
            
            currentUtterance = utterance;
            synth.speak(utterance);
            log('Commande speak() envoyée', 'info');
        }
        
        function testCustom() {
            const text = document.getElementById('customText').value.trim();
            if (!text) {
                alert('Veuillez entrer un texte');
                return;
            }
            
            log('Démarrage du test personnalisé...', 'info');
            stopSpeech();
            
            const voices = synth.getVoices();
            let frenchVoice = voices.find(v => v.lang.startsWith('fr'));
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'fr-FR';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            
            if (frenchVoice) {
                utterance.voice = frenchVoice;
            }
            
            utterance.onstart = function() {
                log('✅ Lecture démarrée', 'success');
            };
            
            utterance.onend = function() {
                log('✅ Lecture terminée', 'success');
            };
            
            utterance.onerror = function(event) {
                log('❌ Erreur: ' + event.error, 'error');
                alert('Erreur: ' + event.error);
            };
            
            currentUtterance = utterance;
            synth.speak(utterance);
            log('Commande speak() envoyée pour: ' + text.substring(0, 50) + '...', 'info');
        }
        
        function stopSpeech() {
            if (synth.speaking) {
                synth.cancel();
                log('Lecture arrêtée', 'info');
            }
        }
        
        window.addEventListener('load', function() {
            checkAPI();
            setTimeout(function() {
                listVoices();
            }, 500);
        });
    </script>
</body>
</html>
