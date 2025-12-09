<?php
/**
 * View/FrontOffice/organisations/show.php
 * Affichage détaillé d'une organisation
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($organisation['name'] ?? 'Organisation') ?> - PeaceConnect</title>
    
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
            <h1><?= htmlspecialchars($organisation['name']) ?></h1>
            <?php if (!empty($organisation['acronym'])): ?>
                <p style="font-size: 1.2rem; opacity: 0.9;"><?= htmlspecialchars($organisation['acronym']) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
                        <div>
                            <span class="badge" style="background-color: var(--color-background); color: var(--color-text); font-size: 1rem; padding: 0.5rem 1rem;">
                                <?= htmlspecialchars($organisation['category'] ?? 'Organisation') ?>
                            </span>
                        </div>
                        <span class="badge" style="background-color: <?= $organisation['status'] === 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $organisation['status'] === 'active' ? '#166534' : '#991b1b' ?>; font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= htmlspecialchars($organisation['status'] === 'active' ? 'Active' : 'Inactive') ?>
                        </span>
                    </div>

                    <div class="grid grid-2" style="gap: 3rem;">
                        <!-- Left Column: Logo & Contact Info -->
                        <div>
                            <?php if (!empty($organisation['logo_path'])): ?>
                                <div style="margin-bottom: 2rem; text-align: center;">
                                    <img src="<?= htmlspecialchars($organisation['logo_path']) ?>" alt="Logo" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                </div>
                            <?php endif; ?>

                            <div class="card" style="background-color: var(--color-background);">
                                <div class="card-body">
                                    <h3 style="margin-bottom: 1rem;">Coordonnées</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <?php if (!empty($organisation['email'])): ?>
                                            <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                                <span>📧</span>
                                                <a href="mailto:<?= htmlspecialchars($organisation['email']) ?>"><?= htmlspecialchars($organisation['email']) ?></a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($organisation['phone'])): ?>
                                            <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                                <span>📞</span>
                                                <a href="tel:<?= htmlspecialchars($organisation['phone']) ?>"><?= htmlspecialchars($organisation['phone']) ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($organisation['website'])): ?>
                                            <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                                <span>🌐</span>
                                                <a href="<?= htmlspecialchars($organisation['website']) ?>" target="_blank" rel="noopener noreferrer">Site web</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php 
                                        $addressParts = [];
                                        if (!empty($organisation['address'])) $addressParts[] = $organisation['address'];
                                        if (!empty($organisation['postal_code'])) $addressParts[] = $organisation['postal_code'];
                                        if (!empty($organisation['city'])) $addressParts[] = $organisation['city'];
                                        if (!empty($organisation['country'])) $addressParts[] = $organisation['country'];
                                        $fullAddress = implode(', ', $addressParts);
                                        ?>
                                        <?php if ($fullAddress): ?>
                                            <li style="margin-bottom: 0.5rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                                                <span>📍</span>
                                                <span><?= htmlspecialchars($fullAddress) ?></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Description & Details -->
                        <div>
                            <?php if (!empty($organisation['description'])): ?>
                                <div style="margin-bottom: 2rem;">
                                    <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem; display: inline-block;">À propos</h3>
                                    <div style="line-height: 1.6;">
                                        <?= nl2br(htmlspecialchars($organisation['description'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($organisation['mission'])): ?>
                                <div style="margin-bottom: 2rem;">
                                    <h3 style="border-bottom: 2px solid var(--color-secondary); padding-bottom: 0.5rem; margin-bottom: 1rem; display: inline-block;">Notre Mission</h3>
                                    <div style="line-height: 1.6;">
                                        <?= nl2br(htmlspecialchars($organisation['mission'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($organisation['vision'])): ?>
                                <div style="margin-bottom: 2rem;">
                                    <h3 style="border-bottom: 2px solid var(--color-accent); padding-bottom: 0.5rem; margin-bottom: 1rem; display: inline-block;">Notre Vision</h3>
                                    <div style="line-height: 1.6;">
                                        <?= nl2br(htmlspecialchars($organisation['vision'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-top: 3rem; text-align: center;">
                        <a href="index.php?action=organisations" class="btn btn-outline">
                            ← Retour à la liste des organisations
                        </a>
                    </div>
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
</body>
</html>

