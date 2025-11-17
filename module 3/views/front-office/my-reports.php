<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Signalements - PeaceConnect</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link rel="stylesheet" href="../../assets/css/module3.css">
    <style>
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .report-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .report-type {
            font-size: 0.875rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .type-conflict { background: #e3f2fd; color: #1976d2; }
        .type-harassment { background: #fff3e0; color: #f57c00; }
        .type-violence { background: #ffebee; color: #c62828; }
        .type-discrimination { background: #f3e5f5; color: #7b1fa2; }
        .report-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .report-desc {
            color: #666;
            font-size: 0.938rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .report-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            color: #888;
            flex-wrap: wrap;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.813rem;
            font-weight: 600;
        }
        .status-pending { background: #fff9c4; color: #f57f17; }
        .status-assigned { background: #e3f2fd; color: #1976d2; }
        .status-resolved { background: #e8f5e9; color: #388e3c; }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #888;
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="../../index.html" class="navbar-brand">
                    <span>🕊️</span>
                    <span>PeaceConnect</span>
                </a>
                <button class="navbar-toggle" aria-label="Menu">☰</button>
                <ul class="navbar-menu">
                    <li><a href="../../index.html">Accueil</a></li>
                    <li><a href="my-reports.php" class="active">Mes signalements</a></li>
                    <li><a href="create-report.html">Créer un signalement</a></li>
                    <li><a href="../back-office/reports.php">Back-Office</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="page-header">
                <h1>📋 Mes Signalements</h1>
                <p>Liste de tous les signalements soumis</p>
            </div>

            <?php
            require_once "../../controller/reportController.php";
            $rc = new reportController();
            $reports = $rc->listReports();
            
            if (empty($reports)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>Aucun signalement</h3>
                    <p>Vous n'avez pas encore soumis de signalement.</p>
                    <a href="create-report.html" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                        + Créer un signalement
                    </a>
                </div>
            <?php else: ?>
                <div class="reports-grid">
                    <?php foreach($reports as $r): ?>
                        <div class="report-card">
                            <div class="report-header">
                                <span class="report-type type-<?= htmlspecialchars($r['type']) ?>">
                                    <?= ucfirst(htmlspecialchars($r['type'])) ?>
                                </span>
                                <span class="status-badge status-<?= htmlspecialchars($r['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($r['status'])) ?>
                                </span>
                            </div>
                            
                            <h3 class="report-title"><?= htmlspecialchars($r['title']) ?></h3>
                            
                            <p class="report-desc">
                                <?= htmlspecialchars(substr($r['description'], 0, 150)) ?>
                                <?= strlen($r['description']) > 150 ? '...' : '' ?>
                            </p>
                            
                            <div class="report-meta">
                                <?php if ($r['location']): ?>
                                    <span>📍 <?= htmlspecialchars($r['location']) ?></span>
                                <?php endif; ?>
                                
                                <?php if ($r['incident_date']): ?>
                                    <span>📅 <?= date('d/m/Y', strtotime($r['incident_date'])) ?></span>
                                <?php endif; ?>
                                
                                <span>⏱️ <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div style="text-align: center;">
                <p>&copy; 2025 PeaceConnect - Module 3. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/utils.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>

