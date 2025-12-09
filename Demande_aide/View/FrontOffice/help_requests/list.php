<?php
/**
 * View/FrontOffice/help_requests/list.php
 * Liste des demandes d'aide (public)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'Aide - PeaceConnect</title>
    
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
            <h1>Demandes d'Aide</h1>
            <p>Consultez les demandes d'aide et apportez votre soutien</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="margin: 0;">Liste des demandes</h2>
                <a href="index.php?action=help-requests&method=create" class="btn btn-primary">
                    + Nouvelle demande
                </a>
            </div>

            <?php if (empty($requests)): ?>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                        <h3>Aucune demande pour le moment</h3>
                        <p class="text-muted">Soyez le premier à demander de l'aide ou revenez plus tard.</p>
                        <a href="index.php?action=help-requests&method=create" class="btn btn-primary" style="margin-top: 1rem;">Créer une demande</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--color-background);">
                                    <th style="padding: 1rem; text-align: left;">Type</th>
                                    <th style="padding: 1rem; text-align: left;">Situation</th>
                                    <th style="padding: 1rem; text-align: left;">Urgence</th>
                                    <th style="padding: 1rem; text-align: left;">Statut</th>
                                    <th style="padding: 1rem; text-align: left;">Lieu</th>
                                    <th style="padding: 1rem; text-align: left;">Date</th>
                                    <th style="padding: 1rem; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr style="border-bottom: 1px solid var(--color-background);">
                                        <td style="padding: 1rem;">
                                            <span class="badge" style="background-color: var(--color-background); color: var(--color-text);">
                                                <?= htmlspecialchars($req['help_type']) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; font-weight: 500;">
                                            <?= htmlspecialchars(substr($req['situation'], 0, 50)) ?>...
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php 
                                            $urgencyColor = '#fbbf24'; // Jaune par défaut (normal)
                                            if ($req['urgency_level'] === 'high' || $req['urgency_level'] === 'urgent') $urgencyColor = '#ef4444';
                                            if ($req['urgency_level'] === 'low') $urgencyColor = '#10b981';
                                            
                                            $urgencyLabels = [
                                                'low' => 'Basse',
                                                'normal' => 'Moyenne',
                                                'high' => 'Haute',
                                                'urgent' => 'Critique'
                                            ];
                                            ?>
                                            <span style="color: <?= $urgencyColor ?>; font-weight: bold;">
                                                <?= htmlspecialchars($urgencyLabels[$req['urgency_level']] ?? $req['urgency_level']) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php
                                            $statusLabels = [
                                                'pending' => 'En attente',
                                                'in_progress' => 'En cours',
                                                'completed' => 'Résolue',
                                                'rejected' => 'Fermée'
                                            ];
                                            
                                            $statusColors = [
                                                'pending' => ['bg' => '#eff6ff', 'text' => '#1e40af'],
                                                'in_progress' => ['bg' => '#fff7ed', 'text' => '#9a3412'],
                                                'completed' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                                'rejected' => ['bg' => '#fef2f2', 'text' => '#991b1b']
                                            ];
                                            
                                            $sColor = $statusColors[$req['status']] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                            ?>
                                            <span class="badge" style="background-color: <?= $sColor['bg'] ?>; color: <?= $sColor['text'] ?>;">
                                                <?= htmlspecialchars($statusLabels[$req['status']] ?? $req['status']) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; color: var(--color-text-light);">
                                            <?= htmlspecialchars($req['location'] ?? '-') ?>
                                        </td>
                                        <td style="padding: 1rem; color: var(--color-text-light);">
                                            <?= date('d/m/Y', strtotime($req['created_at'])) ?>
                                        </td>
                                        <td style="padding: 1rem; text-align: right;">
                                            <a href="index.php?action=help-requests&id=<?= $req['id'] ?>" class="btn btn-outline btn-sm">
                                                Voir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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

