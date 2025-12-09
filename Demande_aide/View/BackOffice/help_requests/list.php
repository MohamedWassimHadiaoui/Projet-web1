<?php
/**
 * View/BackOffice/help_requests/list.php
 * Liste des demandes d'aide avec filtres et actions CRUD
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Vérifier droit admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$requirePath = __DIR__ . '/../../../Model/help_request_logic.php';
if (!file_exists($requirePath)) {
    // fallback for older path
    $requirePath = __DIR__ . '/../../Model/help_request_logic.php';
}
require_once $requirePath;
// $requests est passé par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Demandes d'Aide - PeaceConnect Admin</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--color-text);
            color: white;
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid var(--color-secondary);
        }
        
        .main-content {
            flex: 1;
            background-color: var(--color-background);
            padding: 2rem;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .admin-header h1 {
            font-size: 1.75rem;
            margin: 0;
        }
        
        .header-stats {
            display: flex;
            gap: 2rem;
            font-size: 0.875rem;
            color: var(--color-text-light);
        }
        
        .filters-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .filters-card > div {
            display: flex;
            flex-direction: column;
        }
        
        .filters-card label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-text);
        }
        
        .filters-card input,
        .filters-card select {
            padding: 0.5rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-family: inherit;
        }
        
        .filters-card input:focus,
        .filters-card select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(47, 176, 76, 0.1);
        }
        
        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            overflow-x: auto;
        }
        
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-container thead {
            background: var(--color-background);
            border-bottom: 2px solid var(--color-border);
        }
        
        .table-container th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .table-container tbody tr {
            border-bottom: 1px solid var(--color-border);
            transition: background 0.2s;
        }
        
        .table-container tbody tr:hover {
            background: var(--color-background);
        }
        
        .table-container td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--color-text);
        }
        
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .badge-urgent {
            background: #fecaca;
            color: #991b1b;
        }
        
        .badge-high {
            background: #fed7aa;
            color: #92400e;
        }
        
        .badge-normal {
            background: #fef3c7;
            color: #78350f;
        }
        
        .badge-low {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-pending {
            background: #fef3c7;
            color: #78350f;
        }
        
        .badge-in_progress {
            background: #bfdbfe;
            color: #1e3a8a;
        }
        
        .badge-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-rejected {
            background: #fecaca;
            color: #991b1b;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.25rem;
        }
        
        .action-btn {
            padding: 0.5rem;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }
        
        .action-btn-view {
            background: #e0f2fe;
            color: #0369a1;
        }
        
        .action-btn-view:hover {
            background: #0ea5e9;
            color: white;
        }
        
        .action-btn-edit {
            background: #fef3c7;
            color: #92400e;
        }
        
        .action-btn-edit:hover {
            background: #fcd34d;
            color: #78350f;
        }
        
        .action-btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .action-btn-delete:hover {
            background: #fca5a5;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-text-light);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            margin: 0 0 0.5rem 0;
            color: var(--color-text);
        }
        
        .empty-state p {
            margin: 0 0 1.5rem 0;
        }
        
        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .filters-card {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-stats {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                🕊️ PeaceConnect Admin
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?action=dashboard&section=backoffice">📊 Tableau de bord</a></li>
                <li><a href="index.php?action=organisations&section=backoffice">🏢 Organisations</a></li>
                <li><a href="index.php?action=help-requests&section=backoffice" class="active">📋 Demandes d'aide</a></li>
                <li><a href="index.php" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">← Retour au site</a></li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="admin-header">
                <div>
                    <h1>📋 Demandes d'Aide</h1>
                    <div class="header-stats">
                        <div><strong><?= isset($totalRequests) ? $totalRequests : count($requests) ?></strong> demandes</div>
                        <div><strong><?= isset($totalPendingRequests) ? $totalPendingRequests : count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'pending')) ?></strong> en attente</div>
                    </div>
                </div>
                <a href="index.php?action=help-requests&method=create&section=backoffice" class="btn btn-primary">+ Nouvelle demande</a>
            </div>

            <!-- Filtres -->
            <div class="filters-card">
                <div>
                    <label for="filterUrgency">Urgence</label>
                    <select id="filterUrgency">
                        <option value="">Tous</option>
                        <option value="urgent">🔴 Urgent</option>
                        <option value="high">🟠 Élevée</option>
                        <option value="normal">🟡 Normale</option>
                        <option value="low">🟢 Basse</option>
                    </select>
                </div>
                <div>
                    <label for="filterStatus">Statut</label>
                    <select id="filterStatus">
                        <option value="">Tous</option>
                        <option value="pending">⏳ En attente</option>
                        <option value="in_progress">🔄 En cours</option>
                        <option value="completed">✅ Complétée</option>
                        <option value="rejected">❌ Rejetée</option>
                    </select>
                </div>
                <div>
                    <label for="searchInput">Recherche</label>
                    <input type="text" id="searchInput" placeholder="Titre, type...">
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-container">
                <?php if (empty($requests)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h3>Aucune demande d'aide</h3>
                        <p>Commencez en créant votre première demande</p>
                        <a href="index.php?action=help-requests&method=create&section=backoffice" class="btn btn-primary">+ Créer une demande</a>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>

                                <th>Type</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Localisation</th>
                                <th>Date</th>
                                <th style="width: 120px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr data-urgency="<?= strtolower($req['urgency_level'] ?? 'normal') ?>" data-status="<?= strtolower($req['status'] ?? 'pending') ?>">

                                    <td><?= htmlspecialchars($req['help_type'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($req['urgency_level'] ?? 'normal') ?>">
                                            <?php
                                            $urgency = $req['urgency_level'] ?? 'normal';
                                            echo match($urgency) {
                                                'urgent' => '🔴 Urgent',
                                                'high' => '🟠 Élevée',
                                                'normal' => '🟡 Normale',
                                                'low' => '🟢 Basse',
                                                default => 'Normal'
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($req['status'] ?? 'pending') ?>">
                                            <?php
                                            $status = $req['status'] ?? 'pending';
                                            echo match($status) {
                                                'pending' => '⏳ En attente',
                                                'in_progress' => '🔄 En cours',
                                                'completed' => '✅ Complétée',
                                                'rejected' => '❌ Rejetée',
                                                default => 'Inconnu'
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($req['location'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($req['created_at'] ?? 'now')) ?></td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="index.php?action=help-requests&id=<?= $req['id'] ?>&section=backoffice" class="action-btn action-btn-view" title="Voir">👁️</a>
                                            <a href="index.php?action=help-requests&method=edit&id=<?= $req['id'] ?>&section=backoffice" class="action-btn action-btn-edit" title="Éditer">✏️</a>
                                            <form method="POST" action="index.php?action=help-requests&method=delete&id=<?= $req['id'] ?>&section=backoffice" style="display: inline;" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette demande ?\n\nCette action est irréversible.');">
                                                <button type="submit" class="action-btn action-btn-delete" title="Supprimer">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem;">
                    <?php if ($page > 1): ?>
                        <a href="index.php?action=help-requests&section=backoffice&page=<?= $page - 1 ?>" class="btn" style="background: white; border: 1px solid var(--color-border); color: var(--color-text);">
                            &laquo; Précédent
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?action=help-requests&section=backoffice&page=<?= $i ?>" 
                           class="btn"
                           style="<?= $i === $page ? 'background: var(--color-primary); color: white;' : 'background: white; border: 1px solid var(--color-border); color: var(--color-text);' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="index.php?action=help-requests&section=backoffice&page=<?= $page + 1 ?>" class="btn" style="background: white; border: 1px solid var(--color-border); color: var(--color-text);">
                            Suivant &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Filtres en temps réel
        const filterUrgency = document.getElementById('filterUrgency');
        const filterStatus = document.getElementById('filterStatus');
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('tbody tr');

        function filterTable() {
            const urgency = filterUrgency?.value.toLowerCase() || '';
            const status = filterStatus?.value.toLowerCase() || '';
            const search = searchInput?.value.toLowerCase() || '';

            rows.forEach(row => {
                const rowUrgency = row.dataset.urgency || '';
                const rowStatus = row.dataset.status || '';
                const typeCell = row.cells[0]?.textContent.toLowerCase() || '';

                const matchUrgency = !urgency || rowUrgency.includes(urgency);
                const matchStatus = !status || rowStatus.includes(status);
                const matchSearch = !search || typeCell.includes(search);

                row.style.display = (matchUrgency && matchStatus && matchSearch) ? '' : 'none';
            });
        }

        filterUrgency?.addEventListener('change', filterTable);
        filterStatus?.addEventListener('change', filterTable);
        searchInput?.addEventListener('keyup', filterTable);
    </script>
</body>
</html>

