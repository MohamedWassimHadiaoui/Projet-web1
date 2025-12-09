<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title'] ?? 'Événement'); ?> - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
                <button class="navbar-toggle" aria-label="Menu">☰</button>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined" class="active">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=help">Demander de l'aide</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=login">Connexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Événement Détails -->
    <section class="section">
        <div class="container" style="max-width: 800px;">
            <?php if (!empty($event)): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                        <div>
                            <h1 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                            <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                                <?php
                                $badgeClass = match($event['type']) {
                                    'online' => 'badge-success',
                                    'offline' => 'badge-info',
                                    'hybrid' => 'badge-warning',
                                    default => 'badge-secondary'
                                };
                                $badgeText = match($event['type']) {
                                    'online' => 'En ligne',
                                    'offline' => 'Présentiel',
                                    'hybrid' => 'Hybride',
                                    default => 'Événement'
                                };
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Détails principaux -->
                        <div style="background: #f5f5f5; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <p style="color: var(--color-text-light); margin-bottom: 0.25rem;"><strong>📅 Date & Heure</strong></p>
                                    <p><?php echo htmlspecialchars($event['date_event']); ?></p>
                                </div>
                                <div>
                                    <p style="color: var(--color-text-light); margin-bottom: 0.25rem;"><strong>📍 Lieu</strong></p>
                                    <p><?php echo htmlspecialchars($event['location']); ?></p>
                                </div>
                                <div>
                                    <p style="color: var(--color-text-light); margin-bottom: 0.25rem;"><strong>👥 Participants</strong></p>
                                    <p><?php echo htmlspecialchars($event['participants']); ?> personnes inscrites</p>
                                </div>
                                <div>
                                    <p style="color: var(--color-text-light); margin-bottom: 0.25rem;"><strong>💰 Tarif</strong></p>
                                    <p>Gratuit</p>
                                </div>
                            </div>
                        </div>

                        <!-- Météo Integration -->
                        <div style="margin-bottom: 1.5rem;" id="weather-wrapper">
                             <h2 style="margin-bottom: 1rem;">Météo le jour de l'événement</h2>
                             <div id="weather-info" style="background: linear-gradient(135deg, #6dd5fa 0%, #2980b9 100%); color: white; padding: 1.5rem; border-radius: 1rem; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <p><em>Chargement des prévisions...</em></p>
                             </div>
                        </div>

                        <!-- Description -->
                        <div style="margin-bottom: 1.5rem;">
                            <h2 style="margin-bottom: 1rem;">Description</h2>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        </div>

                        <!-- Tags -->
                        <div style="margin-bottom: 1.5rem;">
                            <p style="color: var(--color-text-light); margin-bottom: 0.5rem;"><strong>Catégories</strong></p>
                            <div>
                                <?php foreach (explode(',', $event['tags']) as $tag): ?>
                                    <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <a href="/TasnimCRUD/index.php?controller=event&action=index" class="btn btn-outline">Retour aux événements</a>
                    <button id="toggleParticipationForm" class="btn btn-primary btn-lg">S'inscrire à cet événement</button>
                </div>

                <!-- Formulaire de Participation (Caché par défaut) -->
                <div id="participationFormContainer" style="display: none; margin-bottom: 2rem;">
                    <div class="card">
                        <h3 class="card-title">Confirmer votre inscription</h3>
                        <p style="margin-bottom: 1.5rem; color: var(--color-text-light);">Remplissez ce formulaire pour participer à <strong><?php echo htmlspecialchars($event['title']); ?></strong></p>
                        
                        <form id="participationForm" action="#" method="POST">
                            <div class="form-group">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" name="participant_name" required placeholder="Votre nom">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="participant_email" required placeholder="votre@email.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone (Optionnel)</label>
                                <input type="tel" class="form-control" name="participant_phone" placeholder="+33 6 12 34 56 78">
                            </div>
                            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                <button type="button" id="cancelParticipation" class="btn btn-outline">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 2rem;">
                    <h2>Événement non trouvé</h2>
                    <p style="color: var(--color-text-light); margin: 1rem 0;">L'événement que vous recherchez n'existe pas ou a été supprimé.</p>
                    <a href="/TasnimCRUD/index.php?controller=event&action=index" class="btn btn-primary">Retour aux événements</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: var(--color-text); color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>

    <!-- Weather Script -->
    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        const eventDate = "<?php echo isset($event['date_event']) ? $event['date_event'] : ''; ?>"; 
        const eventLocation = "<?php echo isset($event['location']) ? htmlspecialchars($event['location']) : ''; ?>";
        // On récupère le type s'il est dispo, sinon string vide
        const eventType = "<?php echo isset($event['type']) ? $event['type'] : ''; ?>";
        
        const weatherContainer = document.getElementById('weather-info');
        const weatherWrapper = document.getElementById('weather-wrapper');

        // Validation de base
        if (!eventDate || !eventLocation || !weatherWrapper) {
             if(weatherWrapper) weatherWrapper.style.display = 'none';
             return;
        }

        // 1. Vérifier si c'est en ligne
        // Si le type est 'online' ou si le lieu contient des mots clés
        const onlineKeywords = ['zoom', 'en ligne', 'online', 'webinar', 'teams', 'google meet', 'discord', 'skype'];
        const isOnline = (eventType === 'online') || onlineKeywords.some(kw => eventLocation.toLowerCase().includes(kw));

        if (isOnline) {
            weatherWrapper.style.display = 'none'; // Pas de météo pour le virtuel
            return;
        }

        const getWeatherDescription = (code) => {
            if (code === 0) return "☀️ Ciel dégagé";
            if (code >= 1 && code <= 3) return "⛅ Partiellement nuageux";
            if (code >= 45 && code <= 48) return "🌫️ Brouillard";
            if (code >= 51 && code <= 67) return "🌧️ Pluie";
            if (code >= 71 && code <= 77) return "❄️ Neige";
            if (code >= 95) return "⛈️ Orage";
            return "Météo variable";
        };

        // Fonction pour trouver la ville
        const findCityCoordinates = async (locationStr) => {
            // Tentative 1 : Recherche exacte
            let url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(locationStr)}&count=1&language=fr&format=json`;
            let req = await fetch(url);
            let data = await req.json();
            if (data.results && data.results.length > 0) return data.results[0];

            // Tentative 2 : Si échec et présence de virgule, on prend le premier terme (Ex: "Paris, Centre" -> "Paris")
            if (locationStr.includes(',')) {
                let city = locationStr.split(',')[0].trim();
                // Si le premier terme est trop court (ex: numero de rue), on évite, mais sinon on tente
                if (city.length > 2) {
                    url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=fr&format=json`;
                    req = await fetch(url);
                    data = await req.json();
                    if (data.results && data.results.length > 0) return data.results[0];
                }
            }
            
            // Tentative 3 : Split par espace si pas de virgule, prendre le premier mot si ça ressemble à une ville ? 
            // Risqué (ex "Centre culturel"). On s'arrête là pour l'instant.
            return null;
        };

        try {
            // API Geocoding
            const geoResult = await findCityCoordinates(eventLocation);
            
            if (!geoResult) {
                // Si on a pas trouvé le lieu, on cache la section météo ou on affiche un message discret
                weatherContainer.innerHTML = '<p>Lieu non trouvé pour l\'affichage météo.</p>';
                return;
            }

            const { latitude, longitude, name } = geoResult;

            // Fetch Météo
            // On utilise l'API open-meteo pour la date précise
            // Note: l'API demande start_date et end_date. Si c'est dans le futur (> 14 jours) c'est une prévision, sinon archive.
            // Pour simplifier ici on utilise le endpoint forecast qui va jusqu'à 16 jours.
            
            // Format date YYYY-MM-DD
            // On suppose que eventDate est déjà au bon format ou on prend la date du jour si vide
            let targetDate = eventDate.split(' ')[0]; // On garde juste la partie date si YYYY-MM-DD HH:MM
            
            const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=weathercode,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${targetDate}&end_date=${targetDate}`;
            
            const wReq = await fetch(weatherUrl);
            const weatherData = await wReq.json();

            if (!weatherData.daily || !weatherData.daily.time || weatherData.daily.time.length === 0) {
                weatherContainer.innerHTML = '<p>Données météo non disponibles pour cette date.</p>';
                return;
            }

            const maxTemp = weatherData.daily.temperature_2m_max[0];
            const minTemp = weatherData.daily.temperature_2m_min[0];
            const weatherCode = weatherData.daily.weathercode[0];
            const desc = getWeatherDescription(weatherCode);

            weatherContainer.innerHTML = `
                <h3 style="margin:0; font-size: 1.5rem; color: white;">${desc}</h3>
                <div style="font-size: 3rem; margin: 10px 0; font-weight: bold;">
                    ${Math.round((maxTemp + minTemp) / 2)}°C
                </div>
                <div style="display: flex; justify-content: center; gap: 20px; font-size: 1.1rem;">
                    <span>🔽 ${minTemp}°C</span>
                    <span>🔼 ${maxTemp}°C</span>
                </div>
                <p style="margin-top: 15px; font-size: 0.9rem; opacity: 0.9;">📍 ${name}</p>
            `;

        } catch (e) {
            console.error("Weather Error:", e);
            weatherContainer.innerHTML = '<p>Météo non disponible.</p>';
        }
    });

    // Gestion du formulaire de participation
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggleParticipationForm');
        const formContainer = document.getElementById('participationFormContainer');
        const cancelBtn = document.getElementById('cancelParticipation');
        const form = document.getElementById('participationForm');

        if (toggleBtn && formContainer) {
            toggleBtn.addEventListener('click', () => {
                formContainer.style.display = 'block';
                formContainer.scrollIntoView({ behavior: 'smooth' });
            });
        }

        if (cancelBtn && formContainer) {
            cancelBtn.addEventListener('click', () => {
                formContainer.style.display = 'none';
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('✅ Votre demande de participation a été enregistrée avec succès !');
                formContainer.style.display = 'none';
                if (toggleBtn) {
                    toggleBtn.textContent = '✓ Déjà inscrit';
                    toggleBtn.disabled = true;
                    toggleBtn.classList.replace('btn-primary', 'btn-secondary');
                }
            });
        }
    });
    </script>
</body>
</html>
