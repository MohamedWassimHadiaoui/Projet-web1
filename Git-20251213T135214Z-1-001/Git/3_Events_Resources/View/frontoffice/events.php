<?php
session_start();
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/eventController.php";
$ec = new EventController();
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$events = $ec->listEvents($search ?: null, $sort);
$typeLabels = ['online' => '🌐 Virtual', 'offline' => '📍 On-Site', 'hybrid' => '🔄 Hybrid'];
$userRole = $_SESSION['user_role'] ?? '';
$canCreate = in_array($userRole, ['admin', 'mediator']);
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header {
            text-align: center;
            padding: 3rem 0;
        }
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .page-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .event-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px var(--shadow);
        }
        .event-header {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            padding: 1.5rem;
            color: #fff;
        }
        .event-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.75rem;
        }
        .event-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        .event-body {
            padding: 1.5rem;
        }
        .event-body p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
        }
        .event-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
        }
        .event-participants {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <h1>Community Events</h1>
                <p>Workshops, trainings and conferences on conflict resolution</p>
                <?php if ($canCreate): ?>
                <a href="create_event.php" class="btn btn-primary" style="margin-top:1rem">📅 Create Event</a>
                <?php endif; ?>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card" style="margin-bottom:1.5rem;padding:1.25rem">
                <form method="GET" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search events..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px">
                    <select name="sort" class="form-control" style="width:auto;min-width:150px">
                        <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Most Recent</option>
                        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="title_asc" <?= ($_GET['sort'] ?? '') === 'title_asc' ? 'selected' : '' ?>>Title (A-Z)</option>
                        <option value="title_desc" <?= ($_GET['sort'] ?? '') === 'title_desc' ? 'selected' : '' ?>>Title (Z-A)</option>
                        <option value="participants" <?= ($_GET['sort'] ?? '') === 'participants' ? 'selected' : '' ?>>Most Participants</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search || isset($_GET['sort'])): ?>
                    <a href="events.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
                <?php if ($search): ?>
                <p style="margin-top:1rem;color:var(--text-muted)">Found <?= count($events) ?> event(s) matching "<?= htmlspecialchars($search) ?>"</p>
                <?php endif; ?>
            </div>

            <?php if (count($events) > 0): ?>
            <div class="events-grid">
                <?php foreach ($events as $e): ?>
                <div class="event-card">
                    <div class="event-header">
                        <div class="event-date">
                            <?= date('l, F j, Y', strtotime($e['date_event'])) ?> at <?= date('g:i A', strtotime($e['date_event'])) ?>
                        </div>
                        <h3><?= htmlspecialchars($e['title']) ?></h3>
                    </div>
                    <div class="event-body">
                        <span class="badge badge-assigned" style="margin-bottom:1rem;display:inline-block"><?= $typeLabels[$e['type']] ?? $e['type'] ?></span>
                        <p><?= htmlspecialchars($e['description'] ?? 'No description available.') ?></p>
                        <div class="event-meta">
                            <div class="event-location">Location: <?= htmlspecialchars($e['location'] ?? 'Location TBA') ?></div>
                            <div class="event-participants">Participants: <?= $e['participants'] ?? 0 ?></div>
                        </div>
                        <div style="margin-top:1rem;display:flex;justify-content:flex-end">
                            <a class="btn btn-sm btn-primary" href="event_details.php?id=<?= (int)$e['id'] ?>">View details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <h3>No Events Scheduled</h3>
                    <p style="color:var(--text-muted)">Check back later for upcoming community events.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>

