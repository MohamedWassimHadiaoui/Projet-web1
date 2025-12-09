<?php
/**
 * View/FrontOffice/organisations/list.php
 * Liste des organisations (public)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisations - PeaceConnect</title>
    
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
                    <li><a href="index.php?action=help-requests">Demandes</a></li>
                    <li><a href="index.php?action=organisations" class="active">Organisations</a></li>
                    <li><a href="index.php?action=login">Connexion</a></li>
                    <li><a href="index.php?action=register" class="btn btn-primary btn-sm">S'inscrire</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <section class="hero" style="padding: 4rem 0; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
        <div class="container">
            <h1>Organisations Partenaires</h1>
            <p>Découvrez les organisations qui œuvrent pour la paix et l'inclusion</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="margin: 0;">Liste des organisations</h2>
                <a href="index.php?action=organisations&method=search" class="btn btn-outline">
                    🔍 Rechercher une organisation
                </a>
            </div>

            <?php if (empty($organisations)): ?>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🏢</div>
                        <h3>Aucune organisation trouvée</h3>
                        <p class="text-muted">Il n'y a pas encore d'organisations enregistrées ou correspondant à vos critères.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-3">
                    <?php foreach ($organisations as $org): ?>
                        <div class="card fade-in">
                            <div class="card-body">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                    <?php if (!empty($org['logo_path'])): ?>
                                        <img src="<?= htmlspecialchars($org['logo_path']) ?>" alt="Logo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background-color: var(--color-background); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                            🏢
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="card-title" style="margin: 0; font-size: 1.1rem;"><?= htmlspecialchars($org['name']) ?></h3>
                                        <?php if (!empty($org['acronym'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($org['acronym']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 1rem;">
                                    <span class="badge" style="background-color: var(--color-background); color: var(--color-text);"><?= htmlspecialchars($org['category'] ?? 'Général') ?></span>
                                    <span class="badge" style="background-color: <?= $org['status'] === 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $org['status'] === 'active' ? '#166534' : '#991b1b' ?>;">
                                        <?= htmlspecialchars($org['status'] === 'active' ? 'Actif' : 'Inactif') ?>
                                    </span>
                                </div>

                                <?php if (!empty($org['description'])): ?>
                                    <p style="color: var(--color-text-light); font-size: 0.9rem; margin-bottom: 1rem;">
                                        <?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="index.php?action=organisations&id=<?= $org['id'] ?>" class="btn btn-primary btn-block">
                                    Voir les détails
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
</body>
</html>
