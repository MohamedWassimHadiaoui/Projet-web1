<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources & Contenus - PeaceConnect</title>
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand">🕊️ PeaceConnect</a>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined" class="active">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=login">Connexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div class="section-title" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.5rem;">
                <div>
                    <h1>Ressources & Articles</h1>
                    <p>Articles, guides et ressources partagés par la communauté</p>
                </div>
                <div>
                    <a href="#toggleFrontForm" id="toggleFrontForm" class="btn btn-primary">➕ Ajouter un contenu</a>
                </div>
            </div>

            <!-- Front create form -->
            <?php if (!empty($_GET['error']) && $_GET['error'] === 'missing_fields'): ?>
                <div class="card" style="background:#fff4f4; border:1px solid #f8d7da; color:#842029; padding:0.75rem; margin-bottom:1rem; border-radius:6px;">Le titre et le contenu sont requis.</div>
            <?php endif; ?>
            <div id="frontFormContainer" style="display:none; margin-bottom:1.5rem;">
                <div class="card" style="padding:1rem;">
                    <form id="contenuForm" data-validate method="post" action="/TasnimCRUD/index.php?controller=contenu&action=create_front">
                        <div class="form-group"><label class="form-label">Titre</label><input name="title" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Auteur</label><input name="author" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Tags</label><input name="tags" class="form-control" placeholder="#guide, #ressource"></div>
                        <div class="form-group"><label class="form-label">Contenu</label><textarea name="body" rows="6" class="form-control"></textarea></div>
                        <div style="display:flex; gap:1rem;"><button class="btn btn-primary" type="submit">Publier</button><button type="button" id="cancelFrontForm" class="btn btn-outline">Annuler</button></div>
                    </form>
                </div>
            </div>

            <div class="grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:1.5rem;">
                <?php if (!empty($contenus)): ?>
                    <?php foreach ($contenus as $c): ?>
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($c['title']); ?></h3>
                                <p style="color:#6b7280;"><?php echo htmlspecialchars(substr($c['body'],0,140)) . '...'; ?></p>
                                <div style="display:flex; gap:0.5rem; margin-top:1rem;">
                                            <?php foreach (explode(',', $c['tags'] ?? '') as $tag): if (trim($tag) !== ''): ?>
                                                <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                            <?php endif; endforeach; ?>
                                </div>
                            </div>
                                    <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem;">
                                        <div style="display:flex; align-items:center; gap:0.75rem;">
                                            <small><?php echo htmlspecialchars($c['author'] ?? 'Anonyme'); ?></small>
                                        </div>
                                        <div style="display:flex; gap:0.5rem; align-items:center;">
                                            <a href="/TasnimCRUD/index.php?controller=contenu&action=like&id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm like-btn" data-id="<?php echo $c['id']; ?>">❤ <span class="like-count"><?php echo (int)($c['likes'] ?? 0); ?></span></a>
                                            <a href="/TasnimCRUD/index.php?controller=contenu&action=details&id=<?php echo $c['id']; ?>" class="btn btn-primary btn-sm">Lire</a>
                                        </div>
                                    </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="grid-column:1/-1; text-align:center; padding:2rem;">
                        <div class="empty-state-icon">📭</div>
                        <p>Aucun contenu disponible pour le moment. Proposez un article !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer"><div class="container">&copy; 2024 PeaceConnect</div></footer>

    <script>
        document.getElementById('toggleFrontForm').addEventListener('click', function(e){ e.preventDefault(); var f = document.getElementById('frontFormContainer'); f.style.display = f.style.display === 'none' ? 'block' : 'none'; });
        document.getElementById('cancelFrontForm').addEventListener('click', function(){ document.getElementById('frontFormContainer').style.display = 'none'; });
    </script>
</body>
</html>
