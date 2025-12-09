<?php
/**
 * View/FrontOffice/organisations/search.php
 * Page de recherche pour les organisations (public)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher une Organisation - PeaceConnect</title>
    
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
            <h1>Rechercher une Organisation</h1>
            <p>Trouvez les organisations qui correspondent à vos besoins</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="card" style="margin-bottom: 3rem;">
                <div class="card-body">
                    <h2 style="margin-bottom: 1.5rem;">Critères de recherche</h2>
                    <form method="POST">
                        <div class="grid grid-2" style="gap: 1.5rem;">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" id="name" name="name" class="form-control"
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                       placeholder="Rechercher par nom...">
                            </div>

                            <div class="form-group">
                                <label for="category" class="form-label">Catégorie</label>
                                <input type="text" id="category" name="category" class="form-control"
                                       value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" 
                                       placeholder="Ex: ONG, Association...">
                            </div>

                            <div class="form-group">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" id="city" name="city" class="form-control"
                                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" 
                                       placeholder="Rechercher par ville...">
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Statut</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Tous</option>
                                    <option value="active" <?= (($_POST['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                    <option value="suspended" <?= (($_POST['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspendue</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                🔍 Rechercher
                            </button>
                            <a href="index.php?action=organisations&method=search" class="btn btn-outline">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div style="margin-bottom: 2rem;">
                    <h3>Résultats de recherche</h3>
                    <hr style="border: 0; border-top: 1px solid var(--color-background); margin: 1rem 0;">
                </div>
                
                <?php if (empty($organisations)): ?>
                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 3rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                            <h3>Aucun résultat</h3>
                            <p class="text-muted">Aucune organisation ne correspond à vos critères de recherche.</p>
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

                                    <div class="card-footer">
                                        <a href="index.php?action=organisations&id=<?= $org['id'] ?>" class="btn btn-primary btn-block">
                                            Voir les détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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

