<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contenu['title'] ?? 'Contenu'); ?> - PeaceConnect</title>
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
        <div class="container" style="max-width:800px;">
            <?php if (!empty($contenu)): ?>
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title"><?php echo htmlspecialchars($contenu['title']); ?></h1>
                        <p style="margin:0.5rem 0; color:#6b7280;">par <strong><?php echo htmlspecialchars($contenu['author'] ?? 'Anonyme'); ?></strong> • <?php echo htmlspecialchars($contenu['status']); ?></p>
                        <div style="margin-top:1rem; line-height:1.6;">
                            <?php echo nl2br(htmlspecialchars($contenu['body'])); ?>
                        </div>

                        <div style="display:flex; gap:0.75rem; margin-top:1rem; align-items:center;">
                            <a href="/TasnimCRUD/index.php?controller=contenu&action=like&id=<?php echo $contenu['id']; ?>" class="btn btn-outline like-btn" data-id="<?php echo $contenu['id']; ?>">❤ <span class="like-count"><?php echo (int)($contenu['likes'] ?? 0); ?></span></a>
                            <span style="color:#6b7280; font-size:0.95rem;">Appuyez sur ❤ pour aimer</span>
                        </div>

                        <?php if (!empty($contenu['tags'])): ?>
                        <div style="margin-top:1rem;">
                            <?php foreach (explode(',', $contenu['tags']) as $tag): if (trim($tag) !== ''): ?>
                                <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endif; endforeach; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=contenu&action=index" class="btn btn-outline">← Retour aux contenus</a>
                        <a href="#" class="btn btn-primary">Partager</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card" style="text-align:center; padding:2rem;">Contenu non trouvé</div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer"><div class="container">&copy; 2024 PeaceConnect</div></footer>
</body>
</html>
