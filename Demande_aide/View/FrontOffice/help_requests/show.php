<?php
/**
 * View/FrontOffice/help_requests/show.php
 * Affichage détaillé d'une demande d'aide
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($req['help_type'] ?? 'Demande') ?> - PeaceConnect</title>
    
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
            <h1><?= htmlspecialchars($req['help_type']) ?></h1>
            <p>Détails de la demande d'aide</p>
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
                                <span style="margin-right: 0.5rem;">📍</span>
                                <?= htmlspecialchars($req['location'] ?? 'Non spécifié') ?>
                            </span>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <?php 
                            $urgencyColor = '#fbbf24'; // Jaune par défaut
                            if ($req['urgency_level'] === 'high' || $req['urgency_level'] === 'urgent') $urgencyColor = '#ef4444';
                            if ($req['urgency_level'] === 'low') $urgencyColor = '#10b981';
                            
                            $urgencyLabels = [
                                'low' => 'Basse',
                                'normal' => 'Moyenne',
                                'high' => 'Haute',
                                'urgent' => 'Critique'
                            ];

                            $statusLabels = [
                                'pending' => 'En attente',
                                'in_progress' => 'En cours',
                                'completed' => 'Résolue',
                                'rejected' => 'Fermée'
                            ];
                            ?>
                            <span class="badge" style="background-color: <?= $urgencyColor ?>20; color: <?= $urgencyColor ?>; font-size: 1rem; padding: 0.5rem 1rem; border: 1px solid <?= $urgencyColor ?>;">
                                Urgence : <?= htmlspecialchars($urgencyLabels[$req['urgency_level']] ?? $req['urgency_level']) ?>
                            </span>
                            <span class="badge" style="background-color: #eff6ff; color: #1e40af; font-size: 1rem; padding: 0.5rem 1rem;">
                                <?= htmlspecialchars($statusLabels[$req['status']] ?? $req['status']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap: 3rem;">
                        <!-- Left Column: Situation & Details -->
                        <div>
                            <div style="margin-bottom: 2rem;">
                                <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem; display: inline-block;">Situation</h3>
                                <div style="line-height: 1.6; font-size: 1.1rem;">
                                    <?= nl2br(htmlspecialchars($req['situation'])) ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <p class="text-muted">
                                    <small>Demande créée le <?= date('d/m/Y à H:i', strtotime($req['created_at'])) ?></small>
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Contact & Actions -->
                        <div>
                            <div class="card" style="background-color: var(--color-background);">
                                <div class="card-body">
                                    <h3 style="margin-bottom: 1rem;">Contact</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <?php if (!empty($req['contact_method'])): ?>
                                            <li style="margin-bottom: 1rem;">
                                                <strong style="display: block; margin-bottom: 0.25rem;">Méthode de contact préférée :</strong>
                                                <span><?= htmlspecialchars($req['contact_method']) ?></span>
                                            </li>
                                        <?php endif; ?>


                                    </ul>

                                    <div style="margin-top: 2rem;">
                                        <button class="btn btn-primary btn-block" onclick="alert('Fonctionnalité de contact à venir !')">
                                            Contacter pour aider
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 3rem; text-align: center;">
                        <a href="index.php?action=help-requests" class="btn btn-outline">
                            ← Retour à la liste des demandes
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

