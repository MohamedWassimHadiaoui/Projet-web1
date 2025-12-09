<?php
/**
 * View/BackOffice/login.php
 * Page de connexion administrateur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <style>
        .admin-login-body {
            background: var(--color-bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 400px;
        }
        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #123c8a 0%, #2f6fd8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="admin-login-body">

    <div class="admin-login-card">
        <div class="admin-logo">
            PeaceConnect Admin
        </div>

        <?php if (isset($_SESSION['auth_error'])): ?>
            <div class="alert alert-danger" style="background-color: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; padding: 1rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem;">
                <?= htmlspecialchars($_SESSION['auth_error']) ?>
                <?php unset($_SESSION['auth_error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=admin_login">
            <div class="form-group">
                <label for="admin_password" class="form-label">Mot de passe administrateur</label>
                <input type="password" id="admin_password" name="admin_password" class="form-control" required autofocus>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                    Se connecter
                </button>
            </div>
            
            <div class="text-center mt-3">
                 <a href="index.php" style="color: var(--color-text-light); text-decoration: none; font-size: 0.9rem;">Retour au site</a>
            </div>
        </form>
    </div>

</body>
</html>
