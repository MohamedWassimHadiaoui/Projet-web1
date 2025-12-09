<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PeaceConnect</title>
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand"><span>🕊️</span><span>PeaceConnect</span></a>
                <button class="navbar-toggle" aria-label="Menu">☰</button>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=index">Événements</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=report">Signaler</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=profile">Mon Profil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section" style="min-height: calc(100vh - 200px); display: flex; align-items: center;">
        <div class="container" style="max-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title" style="text-align: center; margin-bottom: 0;">Connexion</h1>
                    <p style="text-align: center; color: var(--color-text-light); margin-top: 0.5rem;">Connectez-vous à votre compte</p>
                </div>
                <form id="loginForm" data-validate novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control">
                        <div class="form-error" aria-live="polite"></div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <div class="form-error" aria-live="polite"></div>
                    </div>
                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter</button>
                    </div>
                    <div style="text-align: center; margin-top: 1rem;">
                        <p style="color: var(--color-text-light);">Pas de compte ? <a href="/TasnimCRUD/index.php?controller=frontoffice&action=register">S'inscrire</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer style="background-color: var(--color-text); color: white; padding: 2rem 0;">
        <div class="container"><div style="text-align: center;"><p>&copy; 2024 PeaceConnect</p></div></div>
    </footer>

    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>
</body>
</html>
