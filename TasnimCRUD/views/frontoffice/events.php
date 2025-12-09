<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #1e3a8a;
        }

        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            color: #1e40af;
        }

        .section {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .section-title p {
            font-size: 1.125rem;
            color: #6b7280;
        }

        /* Filters Section */
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
            background: white;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        /* Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        /* Event Card */
        .card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-4px);
        }

        .card-body {
            padding: 1.5rem;
            flex: 1;
            position: relative;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
            color: #111827;
            line-height: 1.4;
        }

        .card p {
            color: #6b7280;
            line-height: 1.6;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .event-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.75rem 0;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .event-info span:first-child {
            font-size: 1.125rem;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .tag {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: #f3f4f6;
            color: #374151;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .tag-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .card-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            gap: 0.75rem;
            justify-content: space-between;
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
            text-align: center;
            flex: 1;
        }

        .btn-primary {
            background: #1e40af;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(30, 40, 155, 0.3);
        }

        .btn-outline {
            background: white;
            color: #1e40af;
            border: 1px solid #1e40af;
        }

        .btn-outline:hover {
            background: #f0f9ff;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 1.125rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 3rem;
        }

        .pagination-btn {
            padding: 0.625rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #1e40af;
            color: #1e40af;
        }

        .pagination-btn.active {
            background: #1e40af;
            color: white;
            border-color: #1e40af;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Footer */
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

        /* Responsive */
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .navbar-menu {
                gap: 1rem;
                font-size: 0.875rem;
            }

            .section-title h1 {
                font-size: 1.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
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

    <!-- Événements Section -->
    <section class="section">
        <div class="container">
            <div class="section-title" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <h1>Événements de la communauté</h1>
                    <p>Découvrez et participez aux événements locaux et en ligne pour promouvoir la paix et l'inclusion</p>
                </div>
                <div>
                    <a href="#" id="toggleFrontForm" class="btn btn-primary">➕ Ajouter un événement</a>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filters">
                <div class="filter-group">
                    <label class="form-label">🔍 Rechercher</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Nom de l'événement..." data-validate="search">
                </div>
                <div class="filter-group">
                    <label class="form-label">📍 Type</label>
                    <select id="typeFilter" class="form-control" data-validate="select">
                        <option value="">Tous les types</option>
                        <option value="online">En ligne</option>
                        <option value="offline">Présentiel</option>
                        <option value="hybrid">Hybride</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">📅 Date</label>
                    <select id="dateFilter" class="form-control" data-validate="select">
                        <option value="">Toutes les dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="upcoming">À venir</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">🏷️ Catégorie</label>
                    <select id="categoryFilter" class="form-control" data-validate="select">
                        <option value="">Toutes les catégories</option>
                        <option value="workshop">Atelier</option>
                        <option value="conference">Conférence</option>
                        <option value="meetup">Rencontre</option>
                        <option value="training">Formation</option>
                    </select>
                </div>
            </div>

            <!-- Formulaire d'ajout (front) -->
            <div id="frontFormContainer" style="display: none; margin-bottom: 1.5rem;">
                <div class="card" style="padding: 1rem;">
                    <form id="eventForm" method="post" action="/TasnimCRUD/index.php?controller=event&action=create_front">
                        <div class="form-group">
                            <label class="form-label" for="title">Titre</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Atelier de médiation">
                            <span class="field-error" id="titleError"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_event">Date et heure</label>
                            <input type="text" id="date_event" name="date_event" class="form-control" placeholder="YYYY-MM-DD HH:MM">
                            <span class="field-error" id="dateError"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="type">Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="">-- Choisir --</option>
                                <option value="online">En ligne</option>
                                <option value="offline">Présentiel</option>
                                <option value="hybrid">Hybride</option>
                            </select>
                            <span class="field-error" id="typeError"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="location">Lieu</label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="Ex: Salle A">
                            <span class="field-error" id="locationError"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="participants">Participants (nombre)</label>
                            <input type="number" id="participants" name="participants" class="form-control" value="0" min="0">
                            <span class="field-error" id="participantsError"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tags">Tags</label>
                            <input type="text" id="tags" name="tags" class="form-control" placeholder="#médiation, #formation">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="6" placeholder="Décrivez l'événement"></textarea>
                            <span class="field-error" id="descriptionError"></span>
                        </div>
                        <div class="form-group" style="display:flex; gap:1rem;">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" id="cancelFrontForm" class="btn btn-outline">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grille d'événements dynamique -->
            <div class="grid" id="eventsGrid">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="card event-card" data-event-id="<?php echo $event['id']; ?>" data-event-type="<?php echo htmlspecialchars($event['type']); ?>">
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
                                
                                <div class="event-info">
                                    <span>📅</span>
                                    <span><?php echo htmlspecialchars($event['date_event']); ?></span>
                                </div>
                                <div class="event-info">
                                    <span>📍</span>
                                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                                <div class="event-info">
                                    <span>👥</span>
                                    <span><?php echo htmlspecialchars($event['participants']); ?> participants</span>
                                </div>
                                
                                <div class="tags">
                                    <?php foreach (explode(',', $event['tags']) as $tag): ?>
                                        <span class="tag tag-primary"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="/TasnimCRUD/index.php?controller=event&action=details&id=<?php echo $event['id']; ?>" class="btn btn-primary">Voir détails</a>
                                <button class="btn btn-outline register-btn" data-event-id="<?php echo $event['id']; ?>">S'inscrire</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <p>Aucun événement disponible pour le moment. Revenez bientôt !</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn" id="prevBtn" disabled>← Précédent</button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn" id="nextBtn">Suivant →</button>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/partials/chatbot_widget.php'; ?>

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
    <script src="/TasnimCRUD/assets/js/events.js"></script>
    <script>
        // Form Validation Functions
        const Validators = {
            search: (value) => {
                if (value.length > 0 && value.length < 2) {
                    return { valid: false, message: 'La recherche doit contenir au moins 2 caractères' };
                }
                return { valid: true };
            },
            select: (value) => {
                return { valid: true };
            }
        };

        // Event Listeners for Filters
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const validation = Validators.search(this.value);
            if (!validation.valid && this.value.length > 0) {
                this.style.borderColor = '#ef4444';
                this.title = validation.message;
            } else {
                this.style.borderColor = '#d1d5db';
                this.title = '';
            }
            filterEvents();
        });

        document.getElementById('typeFilter').addEventListener('change', filterEvents);
        document.getElementById('dateFilter').addEventListener('change', filterEvents);
        document.getElementById('categoryFilter').addEventListener('change', filterEvents);

        // Filter Events Function
        function filterEvents() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const categoryFilter = document.getElementById('categoryFilter').value;

            const cards = document.querySelectorAll('.event-card');
            let visibleCount = 0;

            cards.forEach(card => {
                let show = true;

                // Search filter
                if (searchTerm) {
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const description = card.querySelector('p').textContent.toLowerCase();
                    show = show && (title.includes(searchTerm) || description.includes(searchTerm));
                }

                // Type filter
                if (typeFilter) {
                    show = show && card.dataset.eventType === typeFilter;
                }

                card.style.display = show ? 'flex' : 'none';
                if (show) visibleCount++;
            });

            // Show empty state if no results
            if (visibleCount === 0) {
                let emptyState = document.querySelector('.empty-state');
                if (!emptyState) {
                    const grid = document.getElementById('eventsGrid');
                    emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.innerHTML = `
                        <div class="empty-state-icon">🔍</div>
                        <p>Aucun événement ne correspond à votre recherche.</p>
                    `;
                    grid.appendChild(emptyState);
                }
            } else {
                const emptyState = document.querySelector('.empty-state');
                if (emptyState && visibleCount > 0) {
                    emptyState.remove();
                }
            }
        }

        // Register Button Handler
        document.querySelectorAll('.register-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const eventId = this.dataset.eventId;
                const validation = { valid: true };

                if (validation.valid) {
                    alert('✅ Inscription confirmée pour l\'événement #' + eventId);
                    this.textContent = '✓ Inscrit';
                    this.disabled = true;
                    this.style.opacity = '0.6';
                }
            });
        });

        // Initialize
        console.log('✅ Événements Page Loaded');
        // Toggle for front create form
        const toggleFront = document.getElementById('toggleFrontForm');
        const frontFormContainer = document.getElementById('frontFormContainer');
        const cancelFront = document.getElementById('cancelFrontForm');
        if (toggleFront && frontFormContainer) {
            toggleFront.addEventListener('click', function(e) {
                e.preventDefault();
                frontFormContainer.style.display = frontFormContainer.style.display === 'none' ? 'block' : 'none';
                if (frontFormContainer.style.display === 'block') {
                    window.scrollTo({ top: frontFormContainer.offsetTop - 80, behavior: 'smooth' });
                    document.getElementById('title').focus();
                }
            });
        }
        if (cancelFront) {
            cancelFront.addEventListener('click', function(e){ frontFormContainer.style.display = 'none'; });
        }
    </script>
    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/main.js"></script>
</body>
</html>
