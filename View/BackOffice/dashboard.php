<?php
/**
 * dashboard.php
 * Tableau de bord admin avec gestion CRUD simple des demandes d'aide
 */

session_start();
require_once __DIR__ . '/Model/db.php';

// Afficher les messages
$message = '';
$error = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Récupérer toutes les demandes
$requests = getAllRequests();

// Récupérer une demande si en édition
$editRequest = null;
if (isset($_GET['edit'])) {
    $editRequest = getRequestById((int)$_GET['edit']);
    if (!$editRequest) {
        $error = 'Demande non trouvée';
    }
}
?>
<!DOCTYPE HTML>
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
            transition: all 0.3s ease;
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
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-section {
            background: white;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.875rem;
        }
        
        .table-section {
            background: white;
            border-radius: 4px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table thead {
            background-color: var(--color-background);
        }
        
        table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        
        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        table tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
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
                <li><a href="dashboard.php" class="active">📊 Tableau de bord</a></li>
                <li><a href="reports-management.html">📋 Signalements</a></li>
                <li><a href="#">👥 Utilisateurs</a></li>
                <li><a href="#">💬 Forum</a></li>
                <li><a href="#">📅 Événements</a></li>
                <li><a href="#">⚙️ Paramètres</a></li>
                <li><a href="index.html" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">← Retour au site</a></li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="admin-header">
                <div>
                    <h1>Tableau de bord</h1>
                    <p style="color: var(--color-text-light);">Gestion des demandes d'aide</p>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Formulaire de création/édition -->
            <div class="form-section">
                <h2><?php echo $editRequest ? 'Éditer une demande' : 'Créer une nouvelle demande'; ?></h2>
                
                <form method="post" action="help-request-process.php">
                    <input type="hidden" name="action" value="<?php echo $editRequest ? 'update' : 'create'; ?>">
                    <?php if ($editRequest): ?>
                        <input type="hidden" name="id" value="<?php echo $editRequest['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="help_type">Type d'aide <span style="color:red;">*</span></label>
                        <select id="help_type" name="help_type" required>
                            <option value="">-- Sélectionner un type --</option>
                            <option value="legal" <?php echo ($editRequest && $editRequest['help_type'] === 'legal') ? 'selected' : ''; ?>>Aide juridique</option>
                            <option value="medical" <?php echo ($editRequest && $editRequest['help_type'] === 'medical') ? 'selected' : ''; ?>>Aide médicale</option>
                            <option value="financial" <?php echo ($editRequest && $editRequest['help_type'] === 'financial') ? 'selected' : ''; ?>>Aide financière</option>
                            <option value="psychological" <?php echo ($editRequest && $editRequest['help_type'] === 'psychological') ? 'selected' : ''; ?>>Aide psychologique</option>
                            <option value="other" <?php echo ($editRequest && $editRequest['help_type'] === 'other') ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="urgency_level">Niveau d'urgence <span style="color:red;">*</span></label>
                        <select id="urgency_level" name="urgency_level" required>
                            <option value="">-- Sélectionner un niveau --</option>
                            <option value="low" <?php echo ($editRequest && $editRequest['urgency_level'] === 'low') ? 'selected' : ''; ?>>Basse</option>
                            <option value="medium" <?php echo ($editRequest && $editRequest['urgency_level'] === 'medium') ? 'selected' : ''; ?>>Moyenne</option>
                            <option value="high" <?php echo ($editRequest && $editRequest['urgency_level'] === 'high') ? 'selected' : ''; ?>>Haute</option>
                            <option value="critical" <?php echo ($editRequest && $editRequest['urgency_level'] === 'critical') ? 'selected' : ''; ?>>Critique</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="situation">Description <span style="color:red;">*</span></label>
                        <textarea id="situation" name="situation" required><?php echo $editRequest ? htmlspecialchars($editRequest['situation']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Localisation</label>
                        <input type="text" id="location" name="location" value="<?php echo $editRequest ? htmlspecialchars($editRequest['location'] ?? '') : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_method">Contact</label>
                        <input type="text" id="contact_method" name="contact_method" value="<?php echo $editRequest ? htmlspecialchars($editRequest['contact_method'] ?? '') : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="en_attente" <?php echo ($editRequest && $editRequest['status'] === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                            <option value="en_cours" <?php echo ($editRequest && $editRequest['status'] === 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                            <option value="resolu" <?php echo ($editRequest && $editRequest['status'] === 'resolu') ? 'selected' : ''; ?>>Résolu</option>
                            <option value="fermé" <?php echo ($editRequest && $editRequest['status'] === 'fermé') ? 'selected' : ''; ?>>Fermé</option>
                        </select>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editRequest ? 'Mettre à jour' : 'Créer'; ?>
                        </button>
                        <?php if ($editRequest): ?>
                            <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tableau des demandes -->
            <div class="table-section">
                <h2>Demandes d'aide (<?php echo count($requests); ?>)</h2>
                
                <?php if (count($requests) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?php echo $req['id']; ?></td>
                                    <td><?php echo htmlspecialchars($req['help_type']); ?></td>
                                    <td><?php echo htmlspecialchars($req['urgency_level']); ?></td>
                                    <td><?php echo htmlspecialchars($req['status']); ?></td>
                                    <td><?php echo htmlspecialchars($req['created_at']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="dashboard.php?edit=<?php echo $req['id']; ?>" class="btn btn-secondary btn-sm">Éditer</a>
                                            <a href="help-request-process.php?action=delete&id=<?php echo $req['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--color-text-light);">Aucune demande trouvée.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
