<?php
// views/backoffice/event.php
// Unified backoffice page for events: listing + form
// Expects $events from controller and optional $event for editing
$editing = isset($event) && !empty($event);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Événements - PeaceConnect</title>
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
                    <li><a href="/TasnimCRUD/index.php?controller=event&action=admin" class="active btn btn-primary btn-sm">Admin</a></li>
                     <li><a href="/TasnimCRUD/index.php?controller=contenu&action=admin" class="btn btn-outline btn-sm">Contenus</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                   <h1 style="font-size: 2rem; font-weight: 800; color: #111827;">Administration des événements</h1>
                   <p style="color: #6b7280;">Gérez les événements de la communauté</p>
                </div>
            </div>

            <div class="grid-2">
                <!-- Colonne Gauche: Liste -->
                <div>
                    <div class="card">
                        <div class="card-header" style="text-align: left; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <h2 class="card-title">Liste des événements</h2>
                        </div>
                        
                        <?php if (!empty($events)): ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Date</th>
                                            <th>Lieu</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $e): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($e['title']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($e['date_event']); ?></td>
                                            <td><?php echo htmlspecialchars($e['location']); ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($e['type']); ?></span></td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a class="btn btn-outline btn-sm" href="/TasnimCRUD/index.php?controller=event&action=edit&id=<?php echo $e['id']; ?>">✎</a>
                                                    <a class="btn btn-danger btn-sm" href="/TasnimCRUD/index.php?controller=event&action=delete&id=<?php echo $e['id']; ?>" onclick="return confirm('Supprimer cet événement ?');">✕</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 2rem;">
                                <p>Aucun événement trouvé.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Colonne Droite: Formulaire -->
                <div>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><?php echo $editing ? 'Modifier l\'événement' : 'Ajouter un événement'; ?></h2>
                            <p style="color: #6b7280; font-size: 0.9rem;">Remplissez les informations ci-dessous</p>
                        </div>

                        <form id="eventFormAdmin" method="post" action="/TasnimCRUD/index.php?controller=event&action=<?php echo $editing ? 'update' : 'create'; ?>" novalidate>
                            <?php if ($editing): ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label" for="title">Titre <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Titre de l'événement" value="<?php echo $editing ? htmlspecialchars($event['title']) : ''; ?>">
                                <div class="form-error" id="titleError">Le titre est obligatoire (min 3 caractères).</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="date_event">Date <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="date_event" id="date_event" class="form-control" placeholder="YYYY-MM-DD HH:MM" value="<?php echo $editing ? htmlspecialchars($event['date_event']) : ''; ?>">
                                <div class="form-error" id="dateError">Date invalide (Format: YYYY-MM-DD HH:MM).</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Type <span style="color: #ef4444;">*</span></label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">-- Choisir --</option>
                                    <option value="online" <?php echo ($editing && $event['type'] === 'online') ? 'selected' : ''; ?>>En ligne</option>
                                    <option value="offline" <?php echo ($editing && $event['type'] === 'offline') ? 'selected' : ''; ?>>Présentiel</option>
                                    <option value="hybrid" <?php echo ($editing && $event['type'] === 'hybrid') ? 'selected' : ''; ?>>Hybride</option>
                                </select>
                                <div class="form-error" id="typeError">Veuillez sélectionner un type.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Lieu <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Lieu ou URL" value="<?php echo $editing ? htmlspecialchars($event['location']) : ''; ?>">
                                <div class="form-error" id="locationError">Le lieu est obligatoire.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Participants max</label>
                                <input type="number" min="0" name="participants" id="participants" class="form-control" value="<?php echo $editing ? htmlspecialchars($event['participants']) : '0'; ?>">
                                <div class="form-error" id="participantsError">Le nombre de participants doit être positif.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tags</label>
                                <input type="text" name="tags" id="tags" class="form-control" placeholder="Ex: education, paix" value="<?php echo $editing ? htmlspecialchars($event['tags']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Description détaillée"><?php echo $editing ? htmlspecialchars($event['description']) : ''; ?></textarea>
                                <div class="form-error" id="descriptionError">Description obligatoire (min 10 caractères).</div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; margin-top: 2rem;">
                                <button type="submit" class="btn btn-primary btn-block"><?php echo $editing ? 'Enregistrer' : 'Créer l\'événement'; ?></button>
                                <?php if ($editing): ?>
                                    <a href="/TasnimCRUD/index.php?controller=event&action=admin" class="btn btn-outline btn-block" style="text-align: center;">Annuler</a>
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

    <script src="/TasnimCRUD/assets/js/utils.js"></script>
    <script src="/TasnimCRUD/assets/js/events.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto focus si création
        <?php if(!$editing): ?>
            const titleInput = document.getElementById('title');
            if(titleInput) titleInput.focus();
        <?php endif; ?>

        const form = document.getElementById('eventFormAdmin');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Helper function to show error
                const showError = (id, show) => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.display = show ? 'block' : 'none';
                    }
                };

                // Validate Title
                const title = document.getElementById('title');
                if (!title.value.trim() || title.value.trim().length < 3) {
                    showError('titleError', true);
                    isValid = false;
                } else {
                    showError('titleError', false);
                }

                // Validate Date
                const date = document.getElementById('date_event');
                if (!date.value.trim()) {
                    // Simple check for now, can improve with regex
                   showError('dateError', true);
                   isValid = false;
                } else {
                     // Basic format check YYYY-MM-DD HH:MM
                     const dateRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;
                     if (!dateRegex.test(date.value.trim())) {
                        showError('dateError', true);
                        isValid = false;
                     } else {
                        showError('dateError', false);
                     }
                }

                // Validate Type
                const type = document.getElementById('type');
                if (!type.value) {
                    showError('typeError', true);
                    isValid = false;
                } else {
                    showError('typeError', false);
                }

                // Validate Location
                const location = document.getElementById('location');
                if (!location.value.trim()) {
                    showError('locationError', true);
                    isValid = false;
                } else {
                    showError('locationError', false);
                }

                // Validate Participants
                const participants = document.getElementById('participants');
                if (parseInt(participants.value) < 0) {
                    showError('participantsError', true);
                    isValid = false;
                } else {
                    showError('participantsError', false);
                }

                // Validate Description
                const description = document.getElementById('description');
                if (description.value.trim().length < 10) {
                    showError('descriptionError', true);
                    isValid = false;
                } else {
                    showError('descriptionError', false);
                }

                if (!isValid) {
                    e.preventDefault(); // Stop submission
                } else {
                    // Letting form submit naturally to backend
                    // e.preventDefault();
                    // alert('Validation OK. Envoi...'); 
                }
            });
        }
    });
    </script>
</body>
</html>