<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - PeaceConnect</title>
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
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=report">Signaler</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=profile" class="active">Mon Profil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container" style="max-width: 800px;">
            <div class="card" style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold;">JD</div>
                    <div style="flex: 1;">
                        <h1 style="margin-bottom: 0.5rem;">Jean Dupont</h1>
                        <p style="color: var(--color-text-light);">jean@example.com</p>
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <span class="badge badge-success">Vérifié</span>
                            <span class="badge badge-info">Depuis 2024</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informations personnelles</h2>
                </div>
                <form>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control" value="Jean">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" value="Dupont">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="jean@example.com">
                    </div>
                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
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
