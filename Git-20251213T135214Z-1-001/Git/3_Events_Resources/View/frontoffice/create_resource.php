<?php
session_start();
include __DIR__ . '/partials/header.php';

$userRole = $_SESSION['user_role'] ?? '';
if (!in_array($userRole, ['admin', 'mediator'])) {
    $_SESSION['errors'] = ['You must be a mediator to create resources.'];
    header("Location: " . $frontoffice . "resources.php");
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Resource - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <a class="btn btn-secondary" href="<?= $frontoffice ?>resources.php">← Back to Resources</a>

            <div class="hero" style="margin-top:1.5rem">
                <h1>📝 Create Resource</h1>
                <p>Share your knowledge with the community</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div>
            <?php endif; ?>

            <div class="card">
                <form id="resourceForm" action="<?= $controller ?>contenuController.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="add">

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter a descriptive title" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="body" class="form-control" rows="10" placeholder="Write your resource content here..."><?= htmlspecialchars($old['body'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tags (comma separated)</label>
                        <input type="text" name="tags" class="form-control" placeholder="e.g., mediation, conflict, tips" value="<?= htmlspecialchars($old['tags'] ?? '') ?>">
                    </div>

                    <div class="alert" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);color:var(--text-primary)">
                        ℹ️ Your resource will be reviewed by an admin before being published.
                    </div>

                    <div style="display:flex;gap:1rem;margin-top:1.5rem">
                        <button type="submit" class="btn btn-primary">Submit for Review</button>
                        <a href="<?= $frontoffice ?>resources.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>validation.js"></script>
    <script>
        Validator.init('resourceForm', {
            'title': [{ type: 'required', message: 'Title is required' }, { type: 'minLength', min: 5, message: 'Title must be at least 5 characters' }],
            'body': [{ type: 'required', message: 'Content is required' }, { type: 'minLength', min: 50, message: 'Content must be at least 50 characters' }]
        });
    </script>
</body>
</html>

