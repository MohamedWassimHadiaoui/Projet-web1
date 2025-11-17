<?php
// View: list of help requests
// Expects $requests variable from controller
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Liste des demandes</title>
</head>
<body>
    <h1>Demandes</h1>
    <a href="help-request.php">Créer une nouvelle demande</a>
    <ul>
        <?php foreach ($requests as $r): ?>
            <li>
                <strong><?= htmlspecialchars($r['help_type']) ?></strong> — <?= htmlspecialchars($r['situation']) ?>
                <a href="help-request.php?id=<?= $r['id'] ?>">Voir</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
