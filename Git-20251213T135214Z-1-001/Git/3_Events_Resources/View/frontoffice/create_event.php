<?php
session_start();
include __DIR__ . '/partials/header.php';

$userRole = $_SESSION['user_role'] ?? '';
if (!in_array($userRole, ['admin', 'mediator'])) {
    $_SESSION['errors'] = ['You must be a mediator to create events.'];
    header("Location: " . $frontoffice . "events.php");
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - PeaceConnect</title>
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
            <a class="btn btn-secondary" href="<?= $frontoffice ?>events.php">← Back to Events</a>

            <div class="hero" style="margin-top:1.5rem">
                <h1>📅 Create Event</h1>
                <p>Organize a workshop, training or community gathering</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div>
            <?php endif; ?>

            <div class="card">
                <form id="eventForm" action="<?= $controller ?>eventController.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="add">

                    <div class="form-group">
                        <label>Event Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter event title" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Describe the event..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="form-group">
                            <label>Date & Time</label>
                            <input type="datetime-local" name="date_event" class="form-control" value="<?= htmlspecialchars($old['date_event'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Event Type</label>
                            <select name="type" class="form-control">
                                <option value="offline">📍 On-Site</option>
                                <option value="online">🌐 Online</option>
                                <option value="hybrid">🔄 Hybrid</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g., Community Center, Tunis or Zoom link" value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Tags (comma separated)</label>
                        <input type="text" name="tags" class="form-control" placeholder="e.g., workshop, mediation, free" value="<?= htmlspecialchars($old['tags'] ?? '') ?>">
                    </div>

                    <div class="alert" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);color:var(--text-primary)">
                        ℹ️ Your event will be reviewed by an admin before being published.
                    </div>

                    <div style="display:flex;gap:1rem;margin-top:1.5rem">
                        <button type="submit" class="btn btn-primary">Submit for Review</button>
                        <a href="<?= $frontoffice ?>events.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>validation.js"></script>
    <script>
        Validator.init('eventForm', {
            'title': [{ type: 'required', message: 'Title is required' }, { type: 'minLength', min: 5, message: 'Title must be at least 5 characters' }],
            'description': [{ type: 'required', message: 'Description is required' }],
            'date_event': [{ type: 'required', message: 'Date is required' }],
            'location': [{ type: 'required', message: 'Location is required' }]
        });
    </script>
</body>
</html>

