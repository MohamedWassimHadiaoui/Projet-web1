<?php
require_once dirname(__DIR__) . '/../controllers/PublicationController.php';
require_once dirname(__DIR__) . '/../models/Publication.php';

$controller = new PublicationController();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        
        if ($_POST['action'] === 'approve') {
            if ($controller->approvePublication($id)) {
                header('Location: moderation.php?success=1');
                exit;
            } else {
                $error = 'Erreur lors de l\'approbation';
            }
        } elseif ($_POST['action'] === 'reject') {
            if ($controller->rejectPublication($id)) {
                header('Location: moderation.php?success=2');
                exit;
            } else {
                $error = 'Erreur lors du rejet';
            }
        }
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) {
        $message = 'Publication approuvée avec succès !';
    } elseif ($_GET['success'] == 2) {
        $message = 'Publication rejetée avec succès !';
    }
}

$pendingPublications = $controller->listPendingPublications();
if ($pendingPublications === false) {
    $pendingPublications = array();
}

$pendingCount = count($pendingPublications);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération - BackOffice</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        .moderation-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .moderation-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-bottom: 2px solid #f59e0b;
        }
        .moderation-body {
            padding: 1.5rem;
        }
        .moderation-content {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            max-height: 200px;
            overflow-y: auto;
        }
        .moderation-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .badge-pending {
            background: #fbbf24;
            color: #78350f;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .meta-info {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'template_header.php'; ?>
    
    <div class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1>📋 Modération des Publications</h1>
                    <p style="color: #6b7280;">
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge-pending"><?php echo $pendingCount; ?> publication(s) en attente</span>
                        <?php else: ?>
                            Aucune publication en attente
                        <?php endif; ?>
                    </p>
                </div>
                <a href="index.php" class="btn btn-outline">← Retour au tableau de bord</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (empty($pendingPublications)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <h2>Aucune publication en attente</h2>
                    <p style="color: #6b7280;">Toutes les publications ont été modérées. Revenez plus tard !</p>
                </div>
            <?php else: ?>
                <?php foreach ($pendingPublications as $pub): ?>
                    <div class="moderation-card">
                        <div class="moderation-header">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <h3 style="margin: 0; color: #78350f;"><?php echo htmlspecialchars($pub->getTitre()); ?></h3>
                                    <div class="meta-info">
                                        <span>👤 <?php echo htmlspecialchars($pub->getAuteur()); ?></span>
                                        <span>📁 <?php echo htmlspecialchars($pub->getCategorie()); ?></span>
                                        <span>📅 <?php echo date('d/m/Y H:i', strtotime($pub->getDateCreation())); ?></span>
                                    </div>
                                </div>
                                <span class="badge-pending">⏳ En attente</span>
                            </div>
                        </div>
                        <div class="moderation-body">
                            <div class="moderation-content">
                                <?php echo nl2br(htmlspecialchars($pub->getContenu())); ?>
                            </div>
                            
                            <?php if ($pub->getTags()): ?>
                                <div style="margin-bottom: 1rem;">
                                    <?php 
                                    $tags = explode(',', $pub->getTags());
                                    foreach ($tags as $tag): 
                                        if (!empty(trim($tag))):
                                    ?>
                                        <span class="tag tag-primary">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="moderation-actions">
                                <form method="POST" action="moderation.php" style="display: inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?php echo $pub->getIdPublication(); ?>">
                                    <button type="submit" class="btn-approve" onclick="return confirm('Approuver cette publication ?');">
                                        ✅ Approuver
                                    </button>
                                </form>
                                
                                <form method="POST" action="moderation.php" style="display: inline;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?php echo $pub->getIdPublication(); ?>">
                                    <button type="submit" class="btn-reject" onclick="return confirm('Rejeter cette publication ?');">
                                        ❌ Rejeter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'template_footer.php'; ?>
</body>
</html>

