<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler - PeaceConnect</title>
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
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=report" class="active">Signaler</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=profile">Mon Profil</a></li>
                    <li><a href="index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container" style="max-width: 700px;">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title" style="text-align: center;">Signaler un incident</h1>
                    <p style="text-align: center; color: var(--color-text-light);">Votre signalement est confidentiel</p>
                </div>

                <form id="reportForm">
                    <div class="form-group">
                        <label class="form-label">Type d'incident</label>
                        <select class="form-control">
                            <option value="">-- Choisir --</option>
                            <option value="violence">Violence</option>
                            <option value="discrimination">Discrimination</option>
                            <option value="harassment">Harcèlement</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lieu</label>
                        <input type="text" class="form-control" placeholder="Adresse ou lieu">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="5" placeholder="Décrivez l'incident..."></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary btn-block">Envoyer le signalement</button>
                    </div>
                </form>
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
