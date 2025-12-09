<?php
/**
 * View/BackOffice/help_requests/form.php
 * Formulaire de création/édition de demande (Admin)
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Protection: only admin can access
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$isEdit = isset($request) && $request;
$formTitle = $isEdit ? 'Éditer la demande' : 'Créer une demande d\'aide';
$submitText = $isEdit ? 'Mettre à jour' : 'Créer';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($formTitle) ?> - Admin</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--color-text);
            color: white;
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid var(--color-secondary);
        }
        
        .main-content {
            flex: 1;
            background-color: var(--color-background);
            padding: 2rem;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .admin-header h1 {
            font-size: 1.75rem;
            margin: 0;
        }
        
        .form-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(47, 176, 76, 0.1);
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--color-border);
        }
        
        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                🕊️ PeaceConnect Admin
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?action=dashboard&section=backoffice">📊 Tableau de bord</a></li>
                <li><a href="index.php?action=organisations&section=backoffice">🏢 Organisations</a></li>
                <li><a href="index.php?action=help-requests&section=backoffice" class="active">📋 Demandes d'aide</a></li>
                <li><a href="index.php" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">← Retour au site</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="admin-header">
                <h1><?= htmlspecialchars($formTitle) ?></h1>
                <a href="index.php?action=help-requests&section=backoffice" class="btn btn-outline">← Retour à la liste</a>
            </div>

            <div class="form-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger" style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                        <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="helpRequestForm" novalidate>
                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="help_type" class="form-label">Type d'aide *</label>
                            <select id="help_type" name="help_type" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <option value="Aide alimentaire" <?= (($request['help_type'] ?? '') === 'Aide alimentaire') ? 'selected' : '' ?>>Aide alimentaire</option>
                                <option value="Soutien psychologique" <?= (($request['help_type'] ?? '') === 'Soutien psychologique') ? 'selected' : '' ?>>Soutien psychologique</option>
                                <option value="Assistance médicale" <?= (($request['help_type'] ?? '') === 'Assistance médicale') ? 'selected' : '' ?>>Assistance médicale</option>
                                <option value="Hébergement" <?= (($request['help_type'] ?? '') === 'Hébergement') ? 'selected' : '' ?>>Hébergement</option>
                                <option value="Transport" <?= (($request['help_type'] ?? '') === 'Transport') ? 'selected' : '' ?>>Transport</option>
                                <option value="Autre" <?= (($request['help_type'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                            </select>
                            <div class="error-message" id="error-help_type"></div>
                        </div>

                        <div class="form-group">
                            <label for="urgency_level" class="form-label">Urgence *</label>
                            <select id="urgency_level" name="urgency_level" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <option value="low" <?= (($request['urgency_level'] ?? '') === 'low') ? 'selected' : '' ?>>Basse</option>
                                <option value="normal" <?= (($request['urgency_level'] ?? '') === 'normal') ? 'selected' : '' ?>>Normale</option>
                                <option value="high" <?= (($request['urgency_level'] ?? '') === 'high') ? 'selected' : '' ?>>Haute</option>
                                <option value="urgent" <?= (($request['urgency_level'] ?? '') === 'urgent') ? 'selected' : '' ?>>Urgent</option>
                            </select>
                            <div class="error-message" id="error-urgency_level"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="situation" class="form-label">Description *</label>
                        <textarea id="situation" name="situation" rows="5" class="form-control"><?= htmlspecialchars($request['situation'] ?? '') ?></textarea>
                        <div class="error-message" id="error-situation"></div>
                    </div>

                    <div class="form-group">
                        <label for="location" class="form-label">Localisation *</label>
                        <input type="text" id="location" name="location" class="form-control"
                               value="<?= htmlspecialchars($request['location'] ?? '') ?>" />
                        <div id="map" style="height: 300px; width: 100%; margin-top: 10px; border-radius: 4px; z-index: 1;"></div>
                        <small class="text-muted" style="display: block; margin-top: 5px; font-size: 0.85em; color: #666;">Cliquez sur la carte pour sélectionner votre position.</small>
                        <div class="error-message" id="error-location"></div>
                    </div>

                    <div class="form-group">
                        <label for="contact_method" class="form-label">Méthode de contact *</label>
                        <select id="contact_method" name="contact_method" class="form-control">
                            <option value="">-- Sélectionner --</option>
                            <option value="Téléphone" <?= (($request['contact_method'] ?? '') === 'Téléphone') ? 'selected' : '' ?>>Téléphone</option>
                            <option value="Email" <?= (($request['contact_method'] ?? '') === 'Email') ? 'selected' : '' ?>>Email</option>
                            <option value="Whatsapp" <?= (($request['contact_method'] ?? '') === 'Whatsapp') ? 'selected' : '' ?>>Whatsapp</option>
                            <option value="Autre" <?= (($request['contact_method'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                        </select>
                        <div class="error-message" id="error-contact_method"></div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Statut *</label>
                        <select id="status" name="status" class="form-control">
                            <option value="pending" <?= (($request['status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>>En attente</option>
                            <option value="in_progress" <?= (($request['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En cours</option>
                            <option value="completed" <?= (($request['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Complétée</option>
                            <option value="rejected" <?= (($request['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>Rejetée</option>
                        </select>
                        <div class="error-message" id="error-status"></div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php?action=help-requests&section=backoffice" class="btn btn-outline">Annuler</a>
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                // Don't disable input in backoffice to allow manual edit if needed, or keep consistent?
                // Let's keep consistent with front office
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
                        locationInput.dispatchEvent(new Event('input')); // Trigger input event too for admin validation
                    });
            });
            // -----------------------
        });
    </script>
    <script>
        // Validation du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('helpRequestForm');
            
            // Fonction pour afficher un message d'erreur
            function showError(fieldId, message) {
                const errorDiv = document.getElementById('error-' + fieldId);
                const field = document.getElementById(fieldId);
                
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                }
                
                if (field) {
                    field.style.borderColor = '#dc2626';
                }
            }
            
            // Fonction pour effacer un message d'erreur
            function clearError(fieldId) {
                const errorDiv = document.getElementById('error-' + fieldId);
                const field = document.getElementById(fieldId);
                
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
                
                if (field) {
                    field.style.borderColor = '';
                }
            }
            
            // Effacer les erreurs lors de la saisie
            const fields = ['help_type', 'urgency_level', 'situation', 'location', 'contact_method', 'status'];
            
            fields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', () => clearError(fieldId));
                    field.addEventListener('change', () => clearError(fieldId));
                }
            });
            
            // Validation lors de la soumission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;
                
                // Effacer toutes les erreurs précédentes
                fields.forEach(clearError);
                
                // Validation du type d'aide (obligatoire)
                const helpType = document.getElementById('help_type').value;
                if (helpType === '') {
                    showError('help_type', 'Le type d\'aide est obligatoire');
                    isValid = false;
                }
                
                // Validation du niveau d'urgence (obligatoire)
                const urgencyLevel = document.getElementById('urgency_level').value;
                if (urgencyLevel === '') {
                    showError('urgency_level', 'Le niveau d\'urgence est obligatoire');
                    isValid = false;
                }
                
                // Validation de la description (obligatoire)
                const situation = document.getElementById('situation').value.trim();
                if (situation === '') {
                    showError('situation', 'La description est obligatoire');
                    isValid = false;
                } else if (situation.length < 20) {
                    showError('situation', 'La description doit contenir au moins 20 caractères');
                    isValid = false;
                } else if (situation.length > 2000) {
                    showError('situation', 'La description ne doit pas dépasser 2000 caractères');
                    isValid = false;
                }
                
                // Validation de la localisation (obligatoire)
                const location = document.getElementById('location').value.trim();
                if (location === '') {
                    showError('location', 'La localisation est obligatoire');
                    isValid = false;
                } else if (location.length < 3) {
                    showError('location', 'La localisation doit contenir au moins 3 caractères');
                    isValid = false;
                } else if (location.length > 255) {
                    showError('location', 'La localisation ne doit pas dépasser 255 caractères');
                    isValid = false;
                }
                
                // Validation de la méthode de contact (obligatoire)
                const contactMethod = document.getElementById('contact_method').value;
                if (contactMethod === '') {
                    showError('contact_method', 'La méthode de contact est obligatoire');
                    isValid = false;
                }
                
                // Validation du statut (obligatoire)
                const status = document.getElementById('status').value;
                if (status === '') {
                    showError('status', 'Le statut est obligatoire');
                    isValid = false;
                }
                
                // Si tout est valide, soumettre le formulaire
                if (isValid) {
                    form.submit();
                } else {
                    // Faire défiler jusqu'à la première erreur
                    const firstError = document.querySelector('.error-message[style*="display: block"]');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
</body>
</html>
