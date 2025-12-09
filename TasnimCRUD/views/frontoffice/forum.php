<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - PeaceConnect</title>
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
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum" class="active">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=index">Événements</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=report">Signaler</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=profile">Mon Profil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1>Forum de la communauté</h1>
                    <p style="color: var(--color-text-light);">Partagez vos expériences</p>
                </div>
                <button class="btn btn-primary">➕ Nouveau post</button>
            </div>

            <div class="grid grid-1" id="forumPosts">
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">SM</div>
                                <div>
                                    <strong>Sophie Martin</strong>
                                    <p style="font-size: 0.875rem; color: var(--color-text-light); margin: 0;">Il y a 2 heures</p>
                                </div>
                            </div>
                            <span class="tag tag-primary">#soutien</span>
                        </div>
                        <h3 style="margin-bottom: 0.5rem;">Besoin de conseils</h3>
                        <p>Comment signaler un incident en toute sécurité ?</p>
                        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                            <button class="btn btn-outline btn-sm">👍 12</button>
                            <button class="btn btn-outline btn-sm">💬 5</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer style="background-color: var(--color-text); color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container"><div style="text-align: center;"><p>&copy; 2024 PeaceConnect</p></div></div>
    </footer>

    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>
</body>
</html>
