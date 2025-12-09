<?php
/**
 * View/BackOffice/organisations/form.php
 * Formulaire de création/édition d'organisation (Admin)
 */
$isEdit = isset($organisation) && $organisation;
$formTitle = $isEdit ? 'Éditer l\'organisation' : 'Créer une nouvelle organisation';
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
                <li><a href="index.php?action=organisations&section=backoffice" class="active">🏢 Organisations</a></li>
                <li><a href="index.php?action=help-requests&section=backoffice">📋 Demandes d'aide</a></li>
                <li><a href="index.php" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">← Retour au site</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="admin-header">
                <h1><?= htmlspecialchars($formTitle) ?></h1>
                <a href="index.php?action=organisations&section=backoffice" class="btn btn-outline">← Retour à la liste</a>
            </div>

            <div class="form-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger" style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                        <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="organisationForm" novalidate>
                    <div class="form-group">
                        <label for="name" class="form-label">Nom de l'organisation *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($organisation['name'] ?? '') ?>" />
                        <div class="error-message" id="error-name"></div>
                    </div>

                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="acronym" class="form-label">Acronyme *</label>
                            <input type="text" id="acronym" name="acronym" class="form-control"
                                   value="<?= htmlspecialchars($organisation['acronym'] ?? '') ?>" />
                            <div class="error-message" id="error-acronym"></div>
                        </div>

                        <div class="form-group">
                            <label for="category" class="form-label">Catégorie *</label>
                            <select id="category" name="category" class="form-control">
                                <option value="">Sélectionner une catégorie</option>
                                <?php
                                $categories = ['Education', 'Santé', 'Environnement', 'Social', 'Technologie', 'Arts', 'Agriculture', 'Commerce', 'Autre'];
                                foreach ($categories as $cat) {
                                    $selected = (isset($organisation['category']) && $organisation['category'] === $cat) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                            <div class="error-message" id="error-category"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description *</label>
                        <textarea id="description" name="description" rows="4" class="form-control"><?= htmlspecialchars($organisation['description'] ?? '') ?></textarea>
                        <div class="error-message" id="error-description"></div>
                    </div>

                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="text" id="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($organisation['email'] ?? '') ?>" />
                            <div class="error-message" id="error-email"></div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <input type="text" id="phone" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($organisation['phone'] ?? '') ?>" />
                            <div class="error-message" id="error-phone"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="website" class="form-label">Site Web *</label>
                        <input type="text" id="website" name="website" class="form-control"
                               value="<?= htmlspecialchars($organisation['website'] ?? '') ?>" />
                        <div class="error-message" id="error-website"></div>
                    </div>

                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="address" class="form-label">Adresse *</label>
                            <input type="text" id="address" name="address" class="form-control"
                                   value="<?= htmlspecialchars($organisation['address'] ?? '') ?>" />
                            <div class="error-message" id="error-address"></div>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">Ville *</label>
                            <select id="city" name="city" class="form-control">
                                <option value="">Sélectionner une ville</option>
                                <?php
                                $cities = ['Tunis', 'Sfax', 'Sousse', 'Gabes', 'Bizerte', 'Ariana', 'Kairouan', 'Gafsa', 'Monastir', 'Ben Arous', 'Kasserine', 'Medenine', 'Nabeul', 'Tataouine', 'Beja', 'Jendouba', 'Mahdia', 'Siliana', 'Kebili', 'Zaghouan', 'Tozeur', 'Manouba'];
                                sort($cities);
                                foreach ($cities as $c) {
                                    $selected = (isset($organisation['city']) && $organisation['city'] === $c) ? 'selected' : '';
                                    echo "<option value=\"$c\" $selected>$c</option>";
                                }
                                ?>
                            </select>
                            <div class="error-message" id="error-city"></div>
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="postal_code" class="form-label">Code Postal *</label>
                            <input type="text" id="postal_code" name="postal_code" class="form-control"
                                   value="<?= htmlspecialchars($organisation['postal_code'] ?? '') ?>" />
                            <div class="error-message" id="error-postal_code"></div>
                        </div>

                        <div class="form-group">
                            <label for="country" class="form-label">Pays *</label>
                            <select id="country" name="country" class="form-control">
                                <option value="">Sélectionner un pays</option>
                                <?php
                                $countries = ['Tunisie', 'France', 'Algérie', 'Maroc', 'Libye', 'Égypte', 'Canada', 'États-Unis', 'Allemagne', 'Italie', 'Royaume-Uni', 'Autre'];
                                foreach ($countries as $cnt) {
                                    $selected = (isset($organisation['country']) && $organisation['country'] === $cnt) ? 'selected' : '';
                                    echo "<option value=\"$cnt\" $selected>$cnt</option>";
                                }
                                ?>
                            </select>
                            <div class="error-message" id="error-country"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo_path" class="form-label">Chemin du Logo</label>
                        <input type="text" id="logo_path" name="logo_path" class="form-control"
                               value="<?= htmlspecialchars($organisation['logo_path'] ?? '') ?>" />
                        <div class="error-message" id="error-logo_path"></div>
                    </div>

                    <div class="form-group">
                        <label for="mission" class="form-label">Mission *</label>
                        <textarea id="mission" name="mission" rows="3" class="form-control"><?= htmlspecialchars($organisation['mission'] ?? '') ?></textarea>
                        <div class="error-message" id="error-mission"></div>
                    </div>

                    <div class="form-group">
                        <label for="vision" class="form-label">Vision *</label>
                        <textarea id="vision" name="vision" rows="3" class="form-control"><?= htmlspecialchars($organisation['vision'] ?? '') ?></textarea>
                        <div class="error-message" id="error-vision"></div>
                    </div>

                    <div class="grid grid-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="status" class="form-label">Statut *</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?= (($organisation['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($organisation['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= (($organisation['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspendue</option>
                            </select>
                            <div class="error-message" id="error-status"></div>
                        </div>

                        <div class="form-group">
                            <label for="updated_by" class="form-label">Modifié par</label>
                            <input type="text" id="updated_by" name="updated_by" class="form-control"
                                   value="<?= htmlspecialchars($organisation['updated_by'] ?? '') ?>" />
                            <div class="error-message" id="error-updated_by"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php?action=organisations&section=backoffice" class="btn btn-outline">Annuler</a>
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Validation du formulaire et gestion dynamique des villes
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('organisationForm');
            const countrySelect = document.getElementById('country');
            const citySelect = document.getElementById('city');
            
            // Valeur actuelle de la ville (pour l'édition)
            const currentCity = "<?= htmlspecialchars($organisation['city'] ?? '') ?>";
            
            // Données des villes par pays
            const citiesByCountry = {
                'Tunisie': ['Tunis', 'Sfax', 'Sousse', 'Gabes', 'Bizerte', 'Ariana', 'Kairouan', 'Gafsa', 'Monastir', 'Ben Arous', 'Kasserine', 'Medenine', 'Nabeul', 'Tataouine', 'Beja', 'Jendouba', 'Mahdia', 'Siliana', 'Kebili', 'Zaghouan', 'Tozeur', 'Manouba'],
                'France': ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims'],
                'Algérie': ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida', 'Batna', 'Djelfa', 'Sétif', 'Sidi Bel Abbès', 'Biskra'],
                'Maroc': ['Casablanca', 'Rabat', 'Fès', 'Tanger', 'Marrakech', 'Agadir', 'Meknès', 'Oujda', 'Kenitra', 'Tetouan'],
                'Libye': ['Tripoli', 'Benghazi', 'Misrata', 'Tarhuna', 'Al Khums', 'Zawiya', 'Zliten'],
                'Égypte': ['Le Caire', 'Alexandrie', 'Gizeh', 'Shubra El-Kheima', 'Port-Saïd', 'Suez', 'Louxor', 'Mansourah'],
                'Canada': ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Edmonton', 'Ottawa', 'Winnipeg', 'Quebec City'],
                'États-Unis': ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose'],
                'Allemagne': ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Stuttgart', 'Düsseldorf', 'Dortmund'],
                'Italie': ['Rome', 'Milan', 'Naples', 'Turin', 'Palerme', 'Gênes', 'Bologne', 'Florence'],
                'Royaume-Uni': ['Londres', 'Birmingham', 'Glasgow', 'Liverpool', 'Bristol', 'Manchester', 'Sheffield', 'Leeds'],
                'Autre': ['Autre']
            };

            // Fonction pour mettre à jour la liste des villes
            function updateCities() {
                const selectedCountry = countrySelect.value;
                
                // Vider la liste actuelle
                citySelect.innerHTML = '<option value="">Sélectionner une ville</option>';
                
                if (selectedCountry && citiesByCountry[selectedCountry]) {
                    const cities = citiesByCountry[selectedCountry];
                    cities.sort(); // Trier alphabétiquement
                    
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        
                        // Sélectionner si c'est la valeur actuelle
                        if (city === currentCity) {
                            option.selected = true;
                        }
                        
                        citySelect.appendChild(option);
                    });
                } else if (selectedCountry === 'Autre') {
                    // Si "Autre", permettre la saisie libre (ici on met juste une option "Autre" pour simplifier, ou on pourrait transformer en input)
                     const option = document.createElement('option');
                     option.value = 'Autre';
                     option.textContent = 'Autre';
                     if (currentCity === 'Autre') option.selected = true;
                     citySelect.appendChild(option);
                }
            }

            // Écouter les changements de pays
            countrySelect.addEventListener('change', updateCities);

            // Initialiser au chargement
            updateCities();
            
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
            
            // Fonction pour valider un email
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Fonction pour valider une URL
            function isValidUrl(url) {
                const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
                return urlRegex.test(url);
            }
            
            // Fonction pour valider un numéro de téléphone
            function isValidPhone(phone) {
                const phoneRegex = /^[\d\s\+\-\(\)\.]+$/;
                return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 8;
            }
            
            // Fonction pour valider un code postal
            function isValidPostalCode(postalCode) {
                // Accepte différents formats de codes postaux
                const postalRegex = /^[\d\w\s\-]{3,10}$/;
                return postalRegex.test(postalCode);
            }
            
            // Effacer les erreurs lors de la saisie
            const fields = ['name', 'acronym', 'category', 'description', 'email', 'phone', 
                          'website', 'address', 'city', 'postal_code', 'country', 
                          'logo_path', 'mission', 'vision', 'status', 'updated_by'];
            
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
                
                // Validation du nom (obligatoire)
                const name = document.getElementById('name').value.trim();
                const nameRegex = /^[a-zA-Z0-9\s]+$/;
                if (name === '') {
                    showError('name', 'Le nom de l\'organisation est obligatoire');
                    isValid = false;
                } else if (!nameRegex.test(name)) {
                    showError('name', 'Le nom ne doit contenir que des lettres et des chiffres');
                    isValid = false;
                } else if (name.length < 3) {
                    showError('name', 'Le nom doit contenir au moins 3 caractères');
                    isValid = false;
                } else if (name.length > 255) {
                    showError('name', 'Le nom ne doit pas dépasser 255 caractères');
                    isValid = false;
                }
                
                // Validation de l'acronyme (obligatoire)
                const acronym = document.getElementById('acronym').value.trim();
                if (acronym === '') {
                    showError('acronym', 'L\'acronyme est obligatoire');
                    isValid = false;
                } else if (acronym.length < 2) {
                    showError('acronym', 'L\'acronyme doit contenir au moins 2 caractères');
                    isValid = false;
                } else if (acronym.length > 20) {
                    showError('acronym', 'L\'acronyme ne doit pas dépasser 20 caractères');
                    isValid = false;
                }
                
                // Validation de la catégorie (obligatoire)
                const category = document.getElementById('category').value.trim();
                if (category === '') {
                    showError('category', 'La catégorie est obligatoire');
                    isValid = false;
                } else if (category.length < 3) {
                    showError('category', 'La catégorie doit contenir au moins 3 caractères');
                    isValid = false;
                } else if (category.length > 100) {
                    showError('category', 'La catégorie ne doit pas dépasser 100 caractères');
                    isValid = false;
                }
                
                // Validation de la description (obligatoire)
                const description = document.getElementById('description').value.trim();
                if (description === '') {
                    showError('description', 'La description est obligatoire');
                    isValid = false;
                } else if (description.length < 10) {
                    showError('description', 'La description doit contenir au moins 10 caractères');
                    isValid = false;
                } else if (description.length > 1000) {
                    showError('description', 'La description ne doit pas dépasser 1000 caractères');
                    isValid = false;
                }
                
                // Validation de l'email (obligatoire)
                const email = document.getElementById('email').value.trim();
                if (email === '') {
                    showError('email', 'L\'adresse email est obligatoire');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Veuillez entrer une adresse email valide');
                    isValid = false;
                }
                
                // Validation du téléphone (obligatoire)
                const phone = document.getElementById('phone').value.trim();
                if (phone === '') {
                    showError('phone', 'Le numéro de téléphone est obligatoire');
                    isValid = false;
                } else if (!isValidPhone(phone)) {
                    showError('phone', 'Veuillez entrer un numéro de téléphone valide (min. 8 chiffres)');
                    isValid = false;
                }
                
                // Validation du site web (obligatoire)
                const website = document.getElementById('website').value.trim();
                if (website === '') {
                    showError('website', 'Le site web est obligatoire');
                    isValid = false;
                } else if (!isValidUrl(website)) {
                    showError('website', 'Veuillez entrer une URL valide (ex: https://example.com)');
                    isValid = false;
                }
                
                // Validation de l'adresse (obligatoire)
                const address = document.getElementById('address').value.trim();
                if (address === '') {
                    showError('address', 'L\'adresse est obligatoire');
                    isValid = false;
                } else if (address.length < 5) {
                    showError('address', 'L\'adresse doit contenir au moins 5 caractères');
                    isValid = false;
                } else if (address.length > 255) {
                    showError('address', 'L\'adresse ne doit pas dépasser 255 caractères');
                    isValid = false;
                }
                
                // Validation de la ville (obligatoire)
                const city = document.getElementById('city').value.trim();
                if (city === '') {
                    showError('city', 'La ville est obligatoire');
                    isValid = false;
                } else if (city.length < 2) {
                    showError('city', 'La ville doit contenir au moins 2 caractères');
                    isValid = false;
                } else if (city.length > 100) {
                    showError('city', 'La ville ne doit pas dépasser 100 caractères');
                    isValid = false;
                }
                
                // Validation du code postal (obligatoire)
                const postalCode = document.getElementById('postal_code').value.trim();
                const postalRegex = /^\d+$/;
                if (postalCode === '') {
                    showError('postal_code', 'Le code postal est obligatoire');
                    isValid = false;
                } else if (!postalRegex.test(postalCode)) {
                    showError('postal_code', 'Le code postal ne doit contenir que des chiffres');
                    isValid = false;
                }
                
                // Validation du pays (obligatoire)
                const country = document.getElementById('country').value.trim();
                if (country === '') {
                    showError('country', 'Le pays est obligatoire');
                    isValid = false;
                } else if (country.length < 2) {
                    showError('country', 'Le pays doit contenir au moins 2 caractères');
                    isValid = false;
                } else if (country.length > 100) {
                    showError('country', 'Le pays ne doit pas dépasser 100 caractères');
                    isValid = false;
                }
                
                // Validation du chemin du logo (optionnel mais avec contrôle)
                const logoPath = document.getElementById('logo_path').value.trim();
                if (logoPath !== '' && logoPath.length > 255) {
                    showError('logo_path', 'Le chemin du logo ne doit pas dépasser 255 caractères');
                    isValid = false;
                }
                
                // Validation de la mission (obligatoire)
                const mission = document.getElementById('mission').value.trim();
                if (mission === '') {
                    showError('mission', 'La mission est obligatoire');
                    isValid = false;
                } else if (mission.length < 10) {
                    showError('mission', 'La mission doit contenir au moins 10 caractères');
                    isValid = false;
                } else if (mission.length > 1000) {
                    showError('mission', 'La mission ne doit pas dépasser 1000 caractères');
                    isValid = false;
                }
                
                // Validation de la vision (obligatoire)
                const vision = document.getElementById('vision').value.trim();
                if (vision === '') {
                    showError('vision', 'La vision est obligatoire');
                    isValid = false;
                } else if (vision.length < 10) {
                    showError('vision', 'La vision doit contenir au moins 10 caractères');
                    isValid = false;
                } else if (vision.length > 1000) {
                    showError('vision', 'La vision ne doit pas dépasser 1000 caractères');
                    isValid = false;
                }
                
                // Validation du statut (obligatoire)
                const status = document.getElementById('status').value;
                if (status === '') {
                    showError('status', 'Le statut est obligatoire');
                    isValid = false;
                }
                
                // Validation du champ "Modifié par" (optionnel mais avec contrôle)
                const updatedBy = document.getElementById('updated_by').value.trim();
                if (updatedBy !== '' && updatedBy.length > 100) {
                    showError('updated_by', 'Le champ "Modifié par" ne doit pas dépasser 100 caractères');
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
