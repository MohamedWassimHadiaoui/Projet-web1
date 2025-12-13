<?php
session_start();
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/contenuController.php";
$cc = new ContenuController();
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$resources = $cc->listContenus($search ?: null, $sort);
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
    <title>Resources - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header {
            text-align: center;
            padding: 3rem 0;
        }
        .page-header h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: var(--text-muted); font-size: 1.1rem; }
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .resource-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px var(--shadow);
            border-color: var(--primary);
        }
        .resource-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ec4899, #db2777);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .resource-card h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .resource-card p {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .resource-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.85rem;
        }
        .resource-author { color: var(--text-muted); }
        .resource-likes {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #ec4899;
            font-weight: 600;
        }
        .speech-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .speech-btn:hover {
            transform: scale(1.1);
        }
        .speech-btn.speaking {
            background: #ef4444;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <h1>Educational Resources</h1>
                <p>Guides and articles on conflict resolution</p>
                <?php if ($canCreate): ?>
                <a href="create_resource.php" class="btn btn-primary" style="margin-top:1rem">📝 Create Resource</a>
                <?php endif; ?>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card" style="margin-bottom:1.5rem;padding:1.25rem">
                <form method="GET" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search resources..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px">
                    <select name="sort" class="form-control" style="width:auto;min-width:150px">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Most Recent</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title (A-Z)</option>
                        <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Title (Z-A)</option>
                        <option value="likes" <?= $sort === 'likes' ? 'selected' : '' ?>>Most Liked</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search || $sort !== 'newest'): ?>
                    <a href="resources.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
                <?php if ($search): ?>
                <p style="margin-top:1rem;color:var(--text-muted)">Found <?= count($resources) ?> resource(s) matching "<?= htmlspecialchars($search) ?>"</p>
                <?php endif; ?>
            </div>

            <?php if (count($resources) > 0): ?>
            <div class="resources-grid">
                <?php foreach ($resources as $r): ?>
                <div class="resource-card">
                    <div class="resource-icon">📖</div>
                    <h3><?= htmlspecialchars($r['title']) ?></h3>
                    <p><?= htmlspecialchars(substr(strip_tags($r['body'] ?? ''), 0, 120)) ?><?= strlen($r['body'] ?? '') > 120 ? '...' : '' ?></p>
                    <div class="resource-footer">
                        <span class="resource-author">By <?= htmlspecialchars($r['author'] ?? 'Team') ?></span>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <form action="<?= $controller ?>contenuController.php" method="POST" style="display:inline">
                                <input type="hidden" name="action" value="like">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button type="submit" class="speech-btn" title="Like">❤️</button>
                            </form>
                            <span class="resource-likes"><?= (int)($r['likes'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div style="margin-top:1rem;display:flex;gap:0.5rem">
                        <a href="resource_details.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Read Full Article</a>
                        <button type="button" class="speech-btn" title="Read aloud"
                            data-titre="<?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?>"
                            data-contenu="<?= htmlspecialchars(strip_tags($r['body'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            🔊
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <h3>No Resources Available</h3>
                    <p style="color:var(--text-muted)">Check back later for educational content.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>forum_features.js"></script>
</body>
</html>

