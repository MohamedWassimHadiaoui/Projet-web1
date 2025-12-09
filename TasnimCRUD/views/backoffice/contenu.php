<?php
// views/backoffice/contenu.php
// Unified backoffice page for contenus: listing + form
// Expects $contenus from controller and optional $contenu for editing
$editing = isset($contenu) && !empty($contenu);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contenus - PeaceConnect</title>
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/main.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/components.css">
    <link rel="stylesheet" href="/TasnimCRUD/assets/css/responsive.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #f8f9fa; color: #333; }
        
        /* Navbar */
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .navbar-content { display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: #1e3a8a; }
        .navbar-menu { display: flex; list-style: none; gap: 2rem; align-items: center; }
        .navbar-menu a { text-decoration: none; color: #374151; font-weight: 500; transition: color 0.2s; }
        .navbar-menu a:hover, .navbar-menu a.active { color: #1e40af; }

        /* Layout */
        .section { padding: 4rem 0; }
        .grid-2 { display: grid; grid-template-columns: 1fr 400px; gap: 2rem; }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
        
        .card { background: white; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); padding: 2rem; height: 100%; }
        .card-header { margin-bottom: 2rem; text-align: center; }
        .card-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: #111827; }

        /* Form */
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-weight: 600; color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #1e40af; box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        /* Validation Error */
        .form-error { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none; }

        /* Buttons */
        .btn { display: inline-block; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s; font-size: 1rem; text-align: center; }
        .btn-primary { background: #1e40af; color: white; }
        .btn-primary:hover { background: #1e3a8a; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(30, 40, 155, 0.1); }
        .btn-outline { background: transparent; border: 1px solid #1e40af; color: #1e40af; }
        .btn-outline:hover { background: #f0f9ff; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; }
        .btn-block { width: 100%; display: block; }

        /* Table */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
        table th { text-align: left; padding: 1rem; border-bottom: 2px solid #e5e7eb; color: #374151; font-weight: 600; }
        table td { padding: 1rem; border-bottom: 1px solid #f3f4f6; color: #6b7280; }
        table tr:last-child td { border-bottom: none; }
        .badge { padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-gray { background: #f3f4f6; color: #374151; }

        /* Footer */
        .footer { background: #111827; color: white; padding: 2rem 0; margin-top: 4rem; text-align: center; }
        .footer a { color: #d1d5db; text-decoration: none; margin: 0 1.5rem; }
        .footer a:hover { color: white; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/TasnimCRUD/index.php" class="navbar-brand"><span>🕊️</span><span>PeaceConnect</span></a>
                <ul class="navbar-menu">
                    <li><a href="/TasnimCRUD/index.php">Accueil</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=forum">Forum</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=combined">Événements & Contenus</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=frontoffice&action=help">Demander de l'aide</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-outline btn-sm">Admin</a></li>
                    <li><a href="/TasnimCRUD/index.php?controller=contenu&action=admin" class="active btn btn-primary btn-sm">Contenus</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                   <h1 style="font-size: 2rem; font-weight: 800; color: #111827;">Administration des contenus</h1>
                   <p style="color: #6b7280;">Gérez les articles et contenus de la communauté</p>
                </div>
            </div>

            <div class="grid-2">
                <!-- Colonne Gauche: Liste -->
                <div>
                    <div class="card">
                        <div class="card-header" style="text-align: left; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <h2 class="card-title">Liste des contenus</h2>
                        </div>
                        
                        <?php if (!empty($contenus)): ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Auteur</th>
                                            <th>Statut</th>
                                            <th>Likes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contenus as $c): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($c['title']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($c['author']); ?></td>
                                            <td>
                                                <?php 
                                                    $statusClass = $c['status'] === 'published' ? 'badge-success' : 'badge-gray';
                                                    $statusLabel = $c['status'] === 'published' ? 'Publié' : 'Brouillon';
                                                ?>
                                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                            </td>
                                            <td><?php echo (int)($c['likes'] ?? 0); ?></td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a class="btn btn-outline btn-sm" href="/TasnimCRUD/index.php?controller=contenu&action=edit&id=<?php echo $c['id']; ?>">✎</a>
                                                    <a class="btn btn-danger btn-sm" href="/TasnimCRUD/index.php?controller=contenu&action=delete&id=<?php echo $c['id']; ?>" onclick="return confirm('Supprimer ce contenu ?');">✕</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 2rem;">
                                <p>Aucun contenu trouvé.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Colonne Droite: Formulaire -->
                <div>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><?php echo $editing ? 'Modifier le contenu' : 'Créer un contenu'; ?></h2>
                            <p style="color: #6b7280; font-size: 0.9rem;">Remplissez les informations ci-dessous</p>
                        </div>

                        <form id="contenuFormAdmin" method="post" action="/TasnimCRUD/index.php?controller=contenu&action=<?php echo $editing ? 'update' : 'create'; ?>" novalidate>
                            <?php if ($editing): ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($contenu['id']); ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label" for="title">Titre <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Titre du contenu" value="<?php echo $editing ? htmlspecialchars($contenu['title']) : ''; ?>">
                                <div class="form-error" id="titleError">Le titre est obligatoire (min 3 caractères).</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="author">Auteur</label>
                                <input type="text" name="author" id="author" class="form-control" placeholder="Nom de l'auteur" value="<?php echo $editing ? htmlspecialchars($contenu['author']) : ''; ?>">
                                <div class="form-error" id="authorError">L'auteur est obligatoire (min 3 caractères).</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Statut <span style="color: #ef4444;">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="draft" <?php echo ($editing && $contenu['status'] === 'draft') ? 'selected' : ''; ?>>Brouillon</option>
                                    <option value="published" <?php echo ($editing && $contenu['status'] === 'published') ? 'selected' : ''; ?>>Publié</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tags</label>
                                <input type="text" name="tags" id="tags" class="form-control" placeholder="Ex: article, news" value="<?php echo $editing ? htmlspecialchars($contenu['tags']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Contenu <span style="color: #ef4444;">*</span></label>
                                <textarea name="body" id="body" class="form-control" rows="8" placeholder="Rédigez votre contenu ici..."><?php echo $editing ? htmlspecialchars($contenu['body']) : ''; ?></textarea>
                                <div class="form-error" id="bodyError">Le contenu ne peut pas être vide (min 10 caractères).</div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; margin-top: 2rem;">
                                <button type="submit" class="btn btn-primary btn-block"><?php echo $editing ? 'Enregistrer' : 'Créer'; ?></button>
                                <?php if ($editing): ?>
                                    <a href="/TasnimCRUD/index.php?controller=contenu&action=admin" class="btn btn-outline btn-block" style="text-align: center;">Annuler</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p style="margin-bottom: 1rem;">&copy; 2024 PeaceConnect. Tous droits réservés.</p>
            <div>
                <a href="#">Mentions légales</a>
                <a href="#">Confidentialité</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto focus si création
        <?php if(!$editing): ?>
            const titleInput = document.getElementById('title');
            if(titleInput) titleInput.focus();
        <?php endif; ?>

        const form = document.getElementById('contenuFormAdmin');
        
        if (form) {
            // Helper function to show error
            const showError = (id, show) => {
                const el = document.getElementById(id);
                if (el) {
                    el.style.display = show ? 'block' : 'none';
                }
            };

            const validateField = (fieldId, errorId, checkFn) => {
                 const field = document.getElementById(fieldId);
                 if (!field) return true; // Skip if field doesn't exist
                 
                 const isValid = checkFn(field.value);
                 showError(errorId, !isValid);
                 return isValid;
            };

            // Real-time validation listeners
            const fields = [
                { id: 'title', err: 'titleError', fn: val => val.trim().length >= 3 },
                { id: 'author', err: 'authorError', fn: val => val.trim().length >= 3 },
                { id: 'body', err: 'bodyError', fn: val => val.trim().length >= 10 }
            ];

            fields.forEach(f => {
                const el = document.getElementById(f.id);
                if (el) {
                    el.addEventListener('input', () => validateField(f.id, f.err, f.fn));
                    el.addEventListener('blur', () => validateField(f.id, f.err, f.fn));
                }
            });

            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate Title
                if (!validateField('title', 'titleError', val => val.trim().length >= 3)) isValid = false;

                // Validate Author
                // We add an author error container if needed, or create one dynamically if missing?
                // The template currently lacks an 'authorError' div, I should assume I might need to add it or fail gently.
                // Looking at the view, I need to make sure the HTML elements exist.
                // The view has 'titleError', 'bodyError'. It DOES NOT have 'authorError'. 
                // I will add the HTML elements for errors in a separate edit if needed, or rely on alert? 
                // Providing inline feedback is better. I will assume the user wants me to add the necessary HTML structure too if I'm "adding control".

                // Actually, I can't easily add HTML *and* JS in one 'replace_file_content' block if they are far apart. 
                // But wait, the JS is at the bottom. I can use 'multi_replace_file_content' to update both valid HTML structure and JS.
                
                // Let's stick to valid JS here. I'll use a simple approach for missing error divs or just validate what exists, 
                // BUT the user asked to "add a control". So I should probably make sure 'Author' is validated too.
                
                // Let's check Author validation:
                const author = document.getElementById('author');
                // Create error div on the fly if not exists? No, better to have it in HTML.
                // I will proceed with just updating the JS first, and assume I will update the HTML in a second step or if I can do it in one go.
                // Wait, I can do multi_replace to fix both HTML and JS.

                if (!validateField('author', 'authorError', val => val.trim().length >= 3)) isValid = false;

                // Validate Body
                if (!validateField('body', 'bodyError', val => val.trim().length >= 10)) isValid = false;

                if (!isValid) {
                    e.preventDefault(); 
                }
            });
        }
    });
    </script>
</body>
</html>
