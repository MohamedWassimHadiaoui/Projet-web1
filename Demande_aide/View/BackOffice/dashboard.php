<?php
/**
 * View/BackOffice/dashboard.php
 * Dashboard Administrateur - Vue principale
 */
require_once __DIR__ . '/../../Model/help_request_logic.php';
require_once __DIR__ . '/../../Model/organisation_logic.php';

$helpRequests = hr_get_all();
$organisations = org_get_all();
$pendingRequests = count(array_filter($helpRequests, fn($r) => $r['status'] === 'pending'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - PeaceConnect Admin</title>
    
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
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .kpi-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        
        .kpi-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .kpi-card-title {
            font-size: 0.875rem;
            color: var(--color-text-light);
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .kpi-card-icon {
            font-size: 2rem;
        }
        
        .kpi-card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        
        .kpi-card-change {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .kpi-card-change.positive {
            color: var(--color-success);
        }
        
        .kpi-card-change.negative {
            color: var(--color-error);
        }
        
        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
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
                <li><a href="index.php?action=dashboard&section=backoffice" class="active">📊 Tableau de bord</a></li>
                <li><a href="index.php?action=organisations&section=backoffice">🏢 Organisations</a></li>
                <li><a href="index.php?action=help-requests&section=backoffice">📋 Demandes d'aide</a></li>
                <li><a href="index.php" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">← Retour au site</a></li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="admin-header">
                <div>
                    <h1>Tableau de bord</h1>
                    <p style="color: var(--color-text-light);">Vue d'ensemble de la plateforme</p>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="index.php?action=organisations&section=backoffice&method=create" class="btn btn-primary">➕ Nouvelle organisation</a>
                    <a href="index.php?action=help-requests&section=backoffice&method=create" class="btn btn-primary">➕ Nouvelle demande</a>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-card-header">
                        <span class="kpi-card-title">Total Demandes</span>
                        <span class="kpi-card-icon">📋</span>
                    </div>
                    <div class="kpi-card-value"><?= count($helpRequests) ?></div>
                    <div class="kpi-card-change positive">
                        <span>Demandes d'aide enregistrées</span>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card-header">
                        <span class="kpi-card-title">Organisations</span>
                        <span class="kpi-card-icon">🏢</span>
                    </div>
                    <div class="kpi-card-value"><?= count($organisations) ?></div>
                    <div class="kpi-card-change positive">
                        <span>Organisations partenaires</span>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card-header">
                        <span class="kpi-card-title">En attente</span>
                        <span class="kpi-card-icon">⏳</span>
                    </div>
                    <div class="kpi-card-value"><?= $pendingRequests ?></div>
                    <div class="kpi-card-change negative">
                        <span>Demandes à traiter</span>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card-header">
                        <span class="kpi-card-title">En cours</span>
                        <span class="kpi-card-icon">🔄</span>
                    </div>
                    <div class="kpi-card-value"><?= count(array_filter($helpRequests, fn($r) => $r['status'] === 'in_progress')) ?></div>
                    <div class="kpi-card-change positive">
                        <span>Demandes en traitement</span>
                    </div>
                </div>
            </div>

            <!-- Tableaux récents -->
            <div class="table-container" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Demandes récentes</h2>
                    <a href="index.php?action=help-requests&section=backoffice" class="btn btn-outline btn-sm">Voir tout</a>
                </div>
                <?php if (empty($helpRequests)): ?>
                    <p style="color: var(--color-text-light);">Aucune demande.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach (array_slice(array_reverse($helpRequests), 0, 5) as $request): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--color-background); border-radius: var(--radius-md);">
                                <div style="flex: 1;">
                                    <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($request['title']) ?></p>
                                    <p style="margin: 0; font-size: 0.875rem; color: var(--color-text-light);">
                                        <?= htmlspecialchars($request['status']) ?> • <?= htmlspecialchars(substr($request['created_at'], 0, 10)) ?>
                                    </p>
                                </div>
                                <a href="index.php?action=help-requests&section=backoffice&method=edit&id=<?= $request['id'] ?>" class="btn btn-outline btn-sm">Éditer</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Organisations récentes</h2>
                    <a href="index.php?action=organisations&section=backoffice" class="btn btn-outline btn-sm">Voir tout</a>
                </div>
                <?php if (empty($organisations)): ?>
                    <p style="color: var(--color-text-light);">Aucune organisation.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach (array_slice(array_reverse($organisations), 0, 5) as $org): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--color-background); border-radius: var(--radius-md);">
                                <div style="flex: 1;">
                                    <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($org['name']) ?></p>
                                    <p style="margin: 0; font-size: 0.875rem; color: var(--color-text-light);">
                                        <?= htmlspecialchars($org['sector'] ?? 'N/A') ?>
                                    </p>
                                </div>
                                <a href="index.php?action=organisations&section=backoffice&method=edit&id=<?= $org['id'] ?>" class="btn btn-outline btn-sm">Éditer</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>


