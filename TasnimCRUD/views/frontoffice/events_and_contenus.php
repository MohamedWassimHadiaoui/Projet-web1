<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements & Ressources - PeaceConnect</title>
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
    <style>
        .tabs-nav {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        .tab-btn {
            padding: 1rem 2rem;
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        .tab-btn.active {
            color: #1e40af;
            border-bottom-color: #1e40af;
        }
        .tab-btn:hover {
            color: #1e40af;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
            color: #111827;
        }
        .card p {
            color: #6b7280;
            line-height: 1.6;
            margin: 0.5rem 0;
            font-size: 0.95rem;
        }
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0.5rem 0;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .card-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        .btn-primary { background: #1e40af; color: white; }
        .btn-primary:hover { background: #1e3a8a; }
        .btn-outline { background: white; color: #1e40af; border: 1px solid #1e40af; }
        .btn-outline:hover { background: #f0f9ff; }
        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 3rem 2rem;
            background: white;
            border-radius: 0.75rem;
        }
        .tag {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: #f3f4f6;
            color: #374151;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }
        .tag-primary { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand">🕊️ PeaceConnect</a>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined" class="active">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=help">Demander de l'aide</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=login">Connexion</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-primary btn-sm">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <h1 style="margin-bottom: 0.5rem;">Événements & Ressources</h1>
            <p style="color: #6b7280; margin-bottom: 2rem;">Découvrez nos événements et ressources pour promouvoir la paix et l'inclusion</p>

            <!-- Barre de recherche et tri -->
            <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <form method="GET" action="/TasnimCRUD/index.php" id="searchForm" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="controller" value="event">
                    <input type="hidden" name="action" value="combined">
                    
                    <!-- Barre de recherche -->
                    <div style="flex: 1; min-width: 250px; position: relative;">
                        <input 
                            type="text" 
                            name="search" 
                            id="searchInput"
                            placeholder="🔍 Rechercher un événement..." 
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                            style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#1e40af'; this.style.boxShadow='0 0 0 3px rgba(30,64,175,0.1)';"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                        >
                        <?php if (!empty($_GET['search'])): ?>
                            <button 
                                type="button" 
                                onclick="document.getElementById('searchInput').value=''; document.getElementById('searchForm').submit();"
                                style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; padding: 0.25rem; font-size: 1.25rem;"
                                title="Effacer la recherche"
                            >✕</button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sélecteur de tri -->
                    <div style="position: relative;">
                        <select 
                            name="sort" 
                            id="sortSelect"
                            onchange="document.getElementById('searchForm').submit();"
                            style="padding: 0.75rem 2.5rem 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; background: white; cursor: pointer; appearance: none; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#1e40af'; this.style.boxShadow='0 0 0 3px rgba(30,64,175,0.1)';"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                        >
                            <option value="date_desc" <?php echo ($_GET['sort'] ?? 'date_desc') === 'date_desc' ? 'selected' : ''; ?>>📅 Plus récent</option>
                            <option value="date_asc" <?php echo ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : ''; ?>>📅 Plus ancien</option>
                            <option value="title_asc" <?php echo ($_GET['sort'] ?? '') === 'title_asc' ? 'selected' : ''; ?>>🔤 Titre (A-Z)</option>
                            <option value="title_desc" <?php echo ($_GET['sort'] ?? '') === 'title_desc' ? 'selected' : ''; ?>>🔤 Titre (Z-A)</option>
                            <option value="participants_desc" <?php echo ($_GET['sort'] ?? '') === 'participants_desc' ? 'selected' : ''; ?>>👥 Plus de participants</option>
                            <option value="participants_asc" <?php echo ($_GET['sort'] ?? '') === 'participants_asc' ? 'selected' : ''; ?>>👥 Moins de participants</option>
                        </select>
                        <span style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280;">▼</span>
                    </div>
                    
                    <!-- Bouton de recherche -->
                    <button 
                        type="submit" 
                        class="btn btn-primary"
                        style="padding: 0.75rem 1.5rem; white-space: nowrap;"
                    >
                        🔍 Rechercher
                    </button>
                    
                    <!-- Bouton reset si filtres actifs -->
                    <?php if (!empty($_GET['search']) || (!empty($_GET['sort']) && $_GET['sort'] !== 'date_desc')): ?>
                        <a 
                            href="/TasnimCRUD/index.php?controller=event&action=combined" 
                            class="btn btn-outline"
                            style="padding: 0.75rem 1.5rem; white-space: nowrap;"
                        >
                            🔄 Réinitialiser
                        </a>
                    <?php endif; ?>
                </form>
                
                <!-- Indicateur de résultats -->
                <?php if (!empty($_GET['search'])): ?>
                    <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #f0f9ff; border-left: 4px solid #1e40af; border-radius: 0.25rem;">
                        <strong style="color: #1e40af;">Recherche active :</strong> 
                        <span style="color: #374151;">"<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                        <span style="color: #6b7280; margin-left: 0.5rem;">(<?php echo count($events); ?> résultat<?php echo count($events) > 1 ? 's' : ''; ?>)</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Onglets -->
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="events">📅 Événements</button>
                <button class="tab-btn" data-tab="contenus">📚 Ressources & Articles</button>
                <button class="tab-btn" data-tab="assistant">🤖 Assistant</button>
            </div>

            <!-- Contenu Événements -->
            <div id="events" class="tab-content active">
                <div class="grid">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    $badgeClass = match($event['type']) {
                                        'online' => 'badge-success',
                                        'offline' => 'badge-info',
                                        'hybrid' => 'badge-warning',
                                        default => 'badge-secondary'
                                    };
                                    $badgeText = match($event['type']) {
                                        'online' => 'En ligne',
                                        'offline' => 'Présentiel',
                                        'hybrid' => 'Hybride',
                                        default => 'Événement'
                                    };
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                                    <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($event['description'], 0, 120)) . '...'; ?></p>
                                    
                                    <div style="font-size: 0.9rem; color: #6b7280; margin: 1rem 0;">
                                        <div>📅 <?php echo htmlspecialchars($event['date_event']); ?></div>
                                        <div>📍 <?php echo htmlspecialchars($event['location']); ?></div>
                                        <div>👥 <?php echo htmlspecialchars($event['participants']); ?> participants</div>
                                    </div>
                                    
                                    <div style="margin: 1rem 0;">
                                        <?php foreach (explode(',', $event['tags'] ?? '') as $tag): if (trim($tag) !== ''): ?>
                                            <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small><?php echo htmlspecialchars($event['location']); ?></small>
                                    <a href="/TasnimCRUD/index.php?controller=event&action=details&id=<?php echo $event['id']; ?>" class="btn btn-primary">Voir détails</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                            <p>Aucun événement disponible pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contenu Ressources/Articles -->
            <div id="contenus" class="tab-content">
                <div class="grid">
                    <?php if (!empty($contenus)): ?>
                        <?php foreach ($contenus as $c): ?>
                            <div class="card">
                                <div class="card-body">
                                    <span class="badge badge-info"><?php echo htmlspecialchars($c['status']); ?></span>
                                    <h3 class="card-title"><?php echo htmlspecialchars($c['title']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($c['body'], 0, 140)) . '...'; ?></p>
                                    
                                    <div style="font-size: 0.9rem; color: #6b7280; margin: 1rem 0;">
                                        <div>✍️ <strong><?php echo htmlspecialchars($c['author'] ?? 'Anonyme'); ?></strong></div>
                                        <div>❤ <?php echo (int)($c['likes'] ?? 0); ?> likes</div>
                                    </div>
                                    
                                    <div style="margin: 1rem 0;">
                                        <?php foreach (explode(',', $c['tags'] ?? '') as $tag): if (trim($tag) !== ''): ?>
                                            <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>
                                <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <a href="/TasnimCRUD/index.php?controller=contenu&action=like&id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm like-btn" data-id="<?php echo $c['id']; ?>">❤ <span class="like-count"><?php echo (int)($c['likes'] ?? 0); ?></span></a>
                                    </div>
                                    <a href="/TasnimCRUD/index.php?controller=contenu&action=details&id=<?php echo $c['id']; ?>" class="btn btn-primary">Lire</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                            <p>Aucune ressource disponible pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assistant Tab -->
            <div id="assistant" class="tab-content">
                <?php include __DIR__ . '/partials/chatbot_widget.php'; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">&copy; 2024 PeaceConnect</div>
    </footer>

    <script>
        // Toggle tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Show selected tab
                document.getElementById(tabId).classList.add('active');
                this.classList.add('active');
            });
        });
    </script>
    <script src="/TasnimCRUD/assets/js/events.js"></script>
    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>
</body>
</html>
