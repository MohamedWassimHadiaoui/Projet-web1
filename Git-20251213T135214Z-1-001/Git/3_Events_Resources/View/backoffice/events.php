<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/eventController.php";
$ec = new EventController();
$events = $ec->listAllEvents();
$pendingCount = $ec->countPending();
$typeLabels = ['online' => '🌐 Virtual', 'offline' => '📍 On-Site', 'hybrid' => '🔄 Hybrid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - PeaceConnect</title>
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
                        <h1>📅 Events <?php if ($pendingCount > 0): ?><span class="badge badge-pending"><?= $pendingCount ?> pending</span><?php endif; ?></h1>
                        <p>Manage community events</p>
                    </div>
                    <a href="event_form.php" class="btn btn-success">+ New Event</a>
                </div>

                <div class="panel-body">
                <?php if (count($events) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Participants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $e): 
                            $status = $e['status'] ?? 'approved';
                            $statusBadge = ['pending' => 'badge-pending', 'approved' => 'badge-resolved', 'rejected' => 'badge-closed'];
                        ?>
                        <tr>
                            <td><strong>#<?= $e['id'] ?></strong></td>
                            <td><?= htmlspecialchars($e['title']) ?></td>
                            <td><?= date('M d, Y H:i', strtotime($e['date_event'])) ?></td>
                            <td><span class="badge badge-assigned"><?= $typeLabels[$e['type']] ?? $e['type'] ?></span></td>
                            <td><span class="badge <?= $statusBadge[$status] ?? 'badge-pending' ?>"><?= ucfirst($status) ?></span></td>
                            <td><?= htmlspecialchars($e['creator_name'] ?? 'Admin') ?></td>
                            <td><?= $e['participants'] ?></td>
                            <td style="white-space:nowrap">
                                <?php if ($status === 'pending'): ?>
                                <form action="<?= $controller ?>eventController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">✓ Approve</button>
                                </form>
                                <form action="<?= $controller ?>eventController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">✗ Reject</button>
                                </form>
                                <?php endif; ?>
                                <a href="event_form.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="<?= $controller ?>eventController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No events yet</p>
                    <a href="event_form.php" class="btn btn-success">+ Create First Event</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
