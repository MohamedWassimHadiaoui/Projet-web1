<?php
// View: create / edit form
// Optional: $request for edit
$isEdit = !empty($request);
$action = $isEdit ? 'edit' : 'create';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?= $isEdit ? 'Modifier' : 'Nouveau' ?> - Demande</title>
</head>
<body>
    <h1><?= $isEdit ? 'Modifier' : 'Créer' ?> une demande</h1>
    <form method="POST">
        <label>Type d'aide:<br>
            <input type="text" name="help_type" value="<?= $request['help_type'] ?? '' ?>" required>
        </label><br>
        <label>Urgence:<br>
            <input type="text" name="urgency_level" value="<?= $request['urgency_level'] ?? '' ?>" required>
        </label><br>
        <label>Situation:<br>
            <textarea name="situation" required><?= $request['situation'] ?? '' ?></textarea>
        </label><br>
        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
