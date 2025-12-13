<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/contenuController.php";
$cc = new ContenuController();

$contenu = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $contenu = $cc->getContenuById($_GET['id']);
    $isEdit = true;
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
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Resource - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit Resource' : 'Add Resource' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="resourceForm" action="../../Controller/contenuController.php" method="POST" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $contenu['id'] ?>"><?php endif; ?>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Resource title" value="<?= htmlspecialchars($contenu['title'] ?? $old['title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="body" class="form-control" rows="6" placeholder="Resource content..."><?= htmlspecialchars($contenu['body'] ?? $old['body'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Author</label>
                                <input type="text" name="author" class="form-control" placeholder="Author name" value="<?= htmlspecialchars($contenu['author'] ?? $old['author'] ?? 'Admin') ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" <?= (($contenu['status'] ?? $old['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= (($contenu['status'] ?? $old['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                                    <option value="archived" <?= (($contenu['status'] ?? $old['status'] ?? '') === 'archived') ? 'selected' : '' ?>>Archived</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tags</label>
                            <input type="text" name="tags" class="form-control" placeholder="#guide, #mediation" value="<?= htmlspecialchars($contenu['tags'] ?? $old['tags'] ?? '') ?>">
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="resources.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script>
        Validator.init('resourceForm', {
            'title': [
                { type: 'required', message: 'Title is required' },
                { type: 'minLength', min: 5, message: 'Title must be at least 5 characters' }
            ],
            'body': [
                { type: 'required', message: 'Content is required' },
                { type: 'minLength', min: 20, message: 'Content must be at least 20 characters' }
            ],
            'author': [
                { type: 'required', message: 'Author is required' }
            ]
        });
    </script>
</body>
</html>


