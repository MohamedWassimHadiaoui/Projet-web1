<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demander de l'aide - PeaceConnect</title>
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #1e3a8a;
        }

        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            color: #1e40af;
        }

        .section {
            padding: 4rem 0;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .card-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
            background: white;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 1rem;
            text-align: center;
        }

        .btn-primary {
            background: #1e40af;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(30, 40, 155, 0.1);
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .footer {
            background: #111827;
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
            text-align: center;
        }

        .footer a {
            color: #d1d5db;
            text-decoration: none;
            margin: 0 1.5rem;
        }

        .footer a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .navbar-menu {
                gap: 1rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand"><span>🕊️</span><span>PeaceConnect</span></a>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=help" class="active">Demander de l'aide</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=login">Connexion</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container" style="max-width: 800px;">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Demander de l'aide</h1>
                    <p style="color: #6b7280;">Nos équipes sont prêtes à vous assister en toute confidentialité</p>
                </div>

                <form id="helpForm">
                    <div class="form-group">
                        <label class="form-label" for="category">Catégorie <span style="color: #ef4444;">*</span></label>
                        <select id="category" class="form-control">
                            <option value="">-- Choisir --</option>
                            <option value="counseling">Conseil psychologique</option>
                            <option value="legal">Aide juridique</option>
                            <option value="resources">Ressources</option>
                            <option value="emergency">Urgence</option>
                            <option value="other">Autre</option>
                        </select>
                        <div class="form-error" id="categoryError" style="display:none; color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">Veuillez sélectionner une catégorie.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="firstname">Prénom <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="firstname" class="form-control" placeholder="Votre prénom">
                        <div class="form-error" id="firstnameError" style="display:none; color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">Le prénom est obligatoire (min 2 caractères).</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email <span style="color: #ef4444;">*</span></label>
                        <input type="email" id="email" class="form-control" placeholder="votre@email.com">
                        <div class="form-error" id="emailError" style="display:none; color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">Veuillez entrer une adresse email valide.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Téléphone (optionnel)</label>
                        <input type="tel" id="phone" class="form-control" placeholder="+33 6 XX XX XX XX">
                        <div class="form-error" id="phoneError" style="display:none; color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">Format de téléphone invalide (chiffres uniquement).</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="situation">Expliquez votre situation <span style="color: #ef4444;">*</span></label>
                        <textarea id="situation" class="form-control" rows="6" placeholder="Décrivez votre situation..."></textarea>
                        <div class="form-error" id="situationError" style="display:none; color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">Veuillez décrire votre situation (min 10 caractères).</div>
                    </div>

                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary btn-block">Envoyer la demande</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            <div>
                <a href="#">Mentions légales</a>
                <a href="#">Confidentialité</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </footer>

    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('helpForm');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    let isValid = true;

                    // Helper function to show error
                    const showError = (id, show) => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.style.display = show ? 'block' : 'none';
                        }
                    };

                    // Validate Category
                    const category = document.getElementById('category');
                    if (!category.value) {
                        showError('categoryError', true);
                        isValid = false;
                    } else {
                        showError('categoryError', false);
                    }

                    // Validate Firstname
                    const firstname = document.getElementById('firstname');
                    if (firstname.value.trim().length < 2) {
                        showError('firstnameError', true);
                        isValid = false;
                    } else {
                        showError('firstnameError', false);
                    }

                    // Validate Email
                    const email = document.getElementById('email');
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email.value.trim())) {
                        showError('emailError', true);
                        isValid = false;
                    } else {
                        showError('emailError', false);
                    }

                    // Validate Phone
                    const phone = document.getElementById('phone');
                    if (phone.value.trim() !== '') {
                        const phoneRegex = /^[\d\+\-\s]{8,}$/;
                        if (!phoneRegex.test(phone.value.trim())) {
                            showError('phoneError', true);
                            isValid = false;
                        } else {
                            showError('phoneError', false);
                        }
                    } else {
                        showError('phoneError', false);
                    }

                    // Validate Situation
                    const situation = document.getElementById('situation');
                    if (situation.value.trim().length < 10) {
                        showError('situationError', true);
                        isValid = false;
                    } else {
                        showError('situationError', false);
                    }

                    if (isValid) {
                        alert('✅ Votre demande a été envoyée avec succès.');
                        form.reset();
                    }
                });
            }
        });
    </script>
</body>
</html>
