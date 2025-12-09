<?php
/**
 * View/FrontOffice/login.php
<?php
/**
 * View/FrontOffice/login.php
 * Page de connexion
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
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
                    <li><a href="index.php?action=login" class="active">Connexion</a></li>
                    <li><a href="index.php?action=register" class="btn btn-primary btn-sm">S'inscrire</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Formulaire de connexion -->
    <section class="section" style="min-height: calc(100vh - 200px); display: flex; align-items: center;">
        <div class="container" style="max-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title" style="text-align: center; margin-bottom: 0;">Connexion</h1>
                    <p style="text-align: center; color: var(--color-text-light); margin-top: 0.5rem;">
                        Connectez-vous à votre compte PeaceConnect
                    </p>
                </div>
                
                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email *</label>
                        <input type="email" id="email" name="email" class="form-control" required autocomplete="email">
                        <span class="form-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe *</label>
                        <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                        <span class="form-error"></span>
                    </div>

                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div class="form-check" style="margin: 0;">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember" class="form-check-label">Se souvenir de moi</label>
                        </div>
                        <a href="#" style="font-size: 0.875rem;">Mot de passe oublié ?</a>
                    </div>

                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            Se connecter
                        </button>
                    </div>

                    <div style="text-align: center; margin-top: 1rem;">
                        <p style="color: var(--color-text-light);">
                            Pas encore de compte ? <a href="index.php?action=register">S'inscrire</a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Options supplémentaires -->
            <div class="card" style="margin-top: 1.5rem; text-align: center;">
                <p style="margin-bottom: 1rem; color: var(--color-text-light);">Ou connectez-vous avec :</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-outline" style="flex: 1;">
                        <span>🔵</span> Google
                    </button>
                    <button class="btn btn-outline" style="flex: 1;">
                        <span>🔷</span> Facebook
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: var(--color-text); color: white; padding: 2rem 0;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
