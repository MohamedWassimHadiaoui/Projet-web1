<?php
/**
 * View/FrontOffice/help_requests/form.php
 * Formulaire de création/édition de demande d'aide
 */
$isEdit = isset($request) && $request;
$formTitle = $isEdit ? 'Éditer la demande' : 'Créer une demande d\'aide';
$submitText = $isEdit ? 'Mettre à jour' : 'Créer la demande';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($formTitle) ?> - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="index.php" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
                <button class="navbar-toggle" aria-label="Menu">☰</button>
                <ul class="navbar-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php?action=help-requests" class="active">Demandes</a></li>
                    <li><a href="index.php?action=organisations">Organisations</a></li>
                    <li><a href="index.php?action=login">Connexion</a></li>
                    <li><a href="index.php?action=register" class="btn btn-primary btn-sm">S'inscrire</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <section class="hero" style="padding: 4rem 0; background: linear-gradient(135deg, var(--color-secondary), var(--color-primary));">
        <div class="container">
            <h1><?= htmlspecialchars($formTitle) ?></h1>
            <p>Remplissez le formulaire ci-dessous pour soumettre votre demande</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container" style="max-width: 800px;">
            <div class="card">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                            <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="helpRequestForm" novalidate>
                        <div class="form-group">
                            <label for="help_type" class="form-label">Type d'aide *</label>
                            <select id="help_type" name="help_type" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="Aide alimentaire" <?= (($request['help_type'] ?? '') === 'Aide alimentaire') ? 'selected' : '' ?>>Aide alimentaire</option>
                                <option value="Soutien psychologique" <?= (($request['help_type'] ?? '') === 'Soutien psychologique') ? 'selected' : '' ?>>Soutien psychologique</option>
                                <option value="Assistance médicale" <?= (($request['help_type'] ?? '') === 'Assistance médicale') ? 'selected' : '' ?>>Assistance médicale</option>
                                <option value="Hébergement" <?= (($request['help_type'] ?? '') === 'Hébergement') ? 'selected' : '' ?>>Hébergement</option>
                                <option value="Transport" <?= (($request['help_type'] ?? '') === 'Transport') ? 'selected' : '' ?>>Transport</option>
                                <option value="Autre" <?= (($request['help_type'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                            </select>
                            <span class="error-message" id="error-help_type" style="color: red; display: none; font-size: 0.875rem; margin-top: 0.25rem;">Ce champ est requis.</span>
                        </div>

                        <div class="form-group">
                            <label for="urgency_level" class="form-label">Niveau d'urgence *</label>
                            <select id="urgency_level" name="urgency_level" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="low" <?= (($request['urgency_level'] ?? '') === 'low') ? 'selected' : '' ?>>Basse</option>
                                <option value="normal" <?= (($request['urgency_level'] ?? '') === 'normal') ? 'selected' : '' ?>>Moyenne</option>
                                <option value="high" <?= (($request['urgency_level'] ?? '') === 'high') ? 'selected' : '' ?>>Haute</option>
                                <option value="urgent" <?= (($request['urgency_level'] ?? '') === 'urgent') ? 'selected' : '' ?>>Critique</option>
                            </select>
                            <span class="error-message" id="error-urgency_level" style="color: red; display: none; font-size: 0.875rem; margin-top: 0.25rem;">Veuillez sélectionner un niveau d'urgence.</span>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">Localisation</label>
                            <input type="text" id="location" name="location" class="form-control"
                                   value="<?= htmlspecialchars($request['location'] ?? '') ?>" 
                                   placeholder="Ville, Quartier...">
                            <div id="map" style="height: 300px; width: 100%; margin-top: 10px; border-radius: 4px; z-index: 1;"></div>
                            <small class="text-muted" style="display: block; margin-top: 5px; font-size: 0.85em; color: #666;">Cliquez sur la carte pour sélectionner votre position.</small>
                        </div>

                        <div class="form-group">
                            <label for="situation" class="form-label">Description de la situation *</label>
                            <textarea id="situation" name="situation" class="form-control" required rows="6" 
                                      placeholder="Décrivez votre situation en détail..."><?= htmlspecialchars($request['situation'] ?? '') ?></textarea>
                        </div>
                        <span class="error-message" id="error-situation" style="color: red; display: none; font-size: 0.875rem; margin-top: 0.25rem;">La description de la situation est requise.</span>

                        <div class="grid grid-2" style="gap: 1.5rem;">
                            <div class="form-group">
                                <label for="contact_method" class="form-label">Méthode de contact préférée</label>
                                <select id="contact_method" name="contact_method" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Téléphone" <?= (($request['contact_method'] ?? '') === 'Téléphone') ? 'selected' : '' ?>>Téléphone</option>
                                    <option value="Email" <?= (($request['contact_method'] ?? '') === 'Email') ? 'selected' : '' ?>>Email</option>
                                    <option value="Whatsapp" <?= (($request['contact_method'] ?? '') === 'Whatsapp') ? 'selected' : '' ?>>Whatsapp</option>
                                    <option value="Autre" <?= (($request['contact_method'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">Statut</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending" <?= (($request['status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>>En attente</option>
                                <option value="in_progress" <?= (($request['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En cours</option>
                                <option value="completed" <?= (($request['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Résolue</option>
                                <option value="rejected" <?= (($request['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>Fermée</option>
                            </select>
                        </div>

                        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                            <a href="index.php?action=help-requests" class="btn btn-outline">Annuler</a>
                            <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: var(--color-text); color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
                <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                    <a href="#" style="color: white;">Mentions légales</a>
                    <a href="#" style="color: white;">Confidentialité</a>
                    <a href="#" style="color: white;">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('helpRequestForm');
            const helpTypeInput = document.getElementById('help_type');
            const urgencyLevelInput = document.getElementById('urgency_level');
            const situationInput = document.getElementById('situation');
            const locationInput = document.getElementById('location');

            // --- Map Integration ---
            // Default position (Tunis) if no location provided
            const defaultLat = 36.8065;
            const defaultLng = 10.1815;
            let map = L.map('map').setView([defaultLat, defaultLng], 13);
            let marker;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Try to get user's current position
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 13);
                    // Don't set marker yet unless we want to auto-fill
                });
            }

            // Handle map click
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }

                // Show loading state
                locationInput.value = "Recherche de l'adresse...";
                locationInput.disabled = true;

                // Reverse geocoding using BigDataCloud (Free, no key, CORS friendly)
                fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=fr`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data) {
                            // Construct a readable address from available fields
                            const parts = [];
                            
                            // Helper to clean strings (remove parentheses like "(l')" often found in country names)
                            const clean = (str) => str.replace(/\s*\(.*?\)\s*/g, '').trim();

                            if (data.locality) parts.push(clean(data.locality));
                            else if (data.city) parts.push(clean(data.city));
                            
                            if (data.principalSubdivision) {
                                const region = clean(data.principalSubdivision);
                                if (!parts.includes(region)) {
                                    parts.push(region);
                                }
                            }
                            
                            if (data.countryName) parts.push(clean(data.countryName));

                            const address = parts.join(', ');
                            locationInput.value = address || "Adresse introuvable";
                        } else {
                            locationInput.value = "Adresse introuvable";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        locationInput.value = "Erreur lors de la récupération de l'adresse";
                    })
                    .finally(() => {
                        locationInput.disabled = false;
                        // Trigger change event for validation
                        locationInput.dispatchEvent(new Event('change'));
                    });
            });
            // -----------------------

            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Reset errors
                document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                // Validate Help Type
                if (!helpTypeInput.value) {
                    isValid = false;
                    document.getElementById('error-help_type').style.display = 'block';
                    helpTypeInput.style.borderColor = 'red';
                }

                // Validate Urgency Level
                if (!urgencyLevelInput.value) {
                    isValid = false;
                    document.getElementById('error-urgency_level').style.display = 'block';
                    urgencyLevelInput.style.borderColor = 'red';
                }

                // Validate Situation
                if (!situationInput.value.trim()) {
                    isValid = false;
                    document.getElementById('error-situation').style.display = 'block';
                    situationInput.style.borderColor = 'red';
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });

            // Real-time validation removal
            [helpTypeInput, urgencyLevelInput, situationInput].forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.style.borderColor = '';
                        const errorId = 'error-' + this.id;
                        const errorEl = document.getElementById(errorId);
                        if (errorEl) errorEl.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>

