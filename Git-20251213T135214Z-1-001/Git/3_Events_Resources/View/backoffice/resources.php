<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/contenuController.php";
$cc = new ContenuController();
$contenus = $cc->listAllContenus();
$pendingCount = $cc->countPending();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources Management - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-header h1 { font-size: 1.5rem; margin: 0; }
        .panel-header p { color: var(--text-muted); margin: 0.25rem 0 0; font-size: 0.9rem; }
        .panel-body { padding: 0; }
        .panel-body .table { margin: 0; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container" style="padding-top: 2rem;">
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h1>📚 Resources <?php if ($pendingCount > 0): ?><span class="badge badge-pending"><?= $pendingCount ?> pending</span><?php endif; ?></h1>
                        <p>Manage educational content</p>
                    </div>
                    <a href="resource_form.php" class="btn btn-success">+ New Resource</a>
                </div>

                <div class="panel-body">
                <?php if (count($contenus) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Likes</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contenus as $c): ?>
                        <tr>
                            <td><strong>#<?= $c['id'] ?></strong></td>
                            <td><?= htmlspecialchars($c['title']) ?></td>
                            <td><?= htmlspecialchars($c['author'] ?? 'Anonymous') ?></td>
                            <td><span class="badge badge-<?= ($c['status'] ?? 'published') === 'published' ? 'resolved' : 'pending' ?>"><?= ucfirst($c['status'] ?? 'published') ?></span></td>
                            <td><?= (int)($c['likes'] ?? 0) ?></td>
                            <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                            <td style="white-space:nowrap">
                                <?php if (($c['status'] ?? '') === 'pending'): ?>
                                <form action="<?= $controller ?>contenuController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">✓ Approve</button>
                                </form>
                                <form action="<?= $controller ?>contenuController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">✗ Reject</button>
                                </form>
                                <?php endif; ?>
                                <a href="resource_form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="<?= $controller ?>contenuController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No resources yet</p>
                    <a href="resource_form.php" class="btn btn-success">+ Create First Resource</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
