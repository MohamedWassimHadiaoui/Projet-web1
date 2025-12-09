<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeaceConnect - Plateforme de Paix et Inclusion</title>
    <meta name="description" content="PeaceConnect, votre plateforme pour signaler, partager et construire ensemble un monde plus pacifique et inclusif.">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand span:first-child {
            font-size: 2rem;
        }
        .navbar-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }
        .navbar-menu a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.2s;
        }
        .navbar-menu a:hover {
            color: #1e40af;
        }
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 5rem 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 1rem;
        }
        .btn-primary {
            background: #16a34a;
            color: white;
        }
        .btn-primary:hover {
            background: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .section {
            padding: 3rem 0;
        }
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-title h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        .section-title p {
            font-size: 1.125rem;
            color: #6b7280;
        }
        .grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        .card:hover {
            border-color: #bfdbfe;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transform: translateY(-4px);
        }
        .card-body {
            padding: 2rem;
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        .card p {
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }
        .card-footer {
            padding: 1rem 2rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .footer {
            background: #111827;
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
            text-align: center;
        }
        .footer a {
            color: #d1d5db;
            text-decoration: none;
            margin: 0 1.5rem;
        }
        .footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="/TasnimCRUD/index.php" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=help">Demander de l'aide</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=login">Connexion</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary" style="display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem;">Admin</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=register" class="btn btn-primary" style="display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem;">S'inscrire</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Bienvenue sur PeaceConnect</h1>
            <p>Votre plateforme pour signaler, partager et construire ensemble un monde plus pacifique et inclusif</p>
            <div class="btn-group">
                <a href="/TasnimCRUD/index.php?controller=frontoffice&action=register" class="btn btn-primary">Commencer maintenant</a>
                <a href="#actions" class="btn btn-outline">En savoir plus</a>
            </div>
        </div>
    </section>

    <!-- Actions Cards Section -->
    <section id="actions" class="section">
        <div class="container">
            <div class="section-title">
                <h2>Que souhaitez-vous faire ?</h2>
                <p>Découvrez les différentes fonctionnalités de PeaceConnect</p>
            </div>
            <div class="grid-4" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));">
                <!-- Carte 1: Signaler -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">📢</div>
                        <h3 class="card-title">Signaler un incident</h3>
                        <p>Signalez en toute sécurité les incidents de violence, discrimination ou harcèlement que vous avez vus ou subis.</p>
                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=frontoffice&action=report" class="btn btn-primary" style="display: inline-block; text-align: center;">Signaler</a>
                    </div>
                </div>

                <!-- Carte 2: Demander de l'aide -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">🤝</div>
                        <h3 class="card-title">Demander de l'aide</h3>
                        <p>Besoin de soutien ? Faites une demande d'aide et recevez des suggestions personnalisées grâce à l'IA.</p>
                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=frontoffice&action=help" class="btn btn-primary" style="display: inline-block; text-align: center;">Demander</a>
                    </div>
                </div>

                <!-- Carte 3: Forum -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">💬</div>
                        <h3 class="card-title">Participer au forum</h3>
                        <p>Échangez avec la communauté, partagez vos expériences et trouvez du soutien auprès d'autres membres.</p>
                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum" class="btn btn-primary" style="display: inline-block; text-align: center;">Voir le forum</a>
                    </div>
                </div>

                <!-- Carte 4: Événements & Contenus -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">📚</div>
                        <h3 class="card-title">Événements & Ressources</h3>
                        <p>Découvrez nos événements et ressources partagés par la communauté pour promouvoir la paix et l'inclusion.</p>
                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=event&action=combined" class="btn btn-primary" style="display: inline-block; text-align: center;">Voir tout</a>
                    </div>
                </div>

                <!-- Carte 5: Ressources Supplémentaires -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">🎓</div>
                        <h3 class="card-title">En apprendre plus</h3>
                        <p>Consultez notre bibliothèque de guides, tutoriels et ressources d'apprentissage pour développer vos compétences.</p>
                    </div>
                    <div class="card-footer">
                        <a href="/TasnimCRUD/index.php?controller=event&action=combined" class="btn btn-primary" style="display: inline-block; text-align: center;">Explorer</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            <div>
                <a href="#">Mentions légales</a>
                <a href="#">Confidentialité</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
