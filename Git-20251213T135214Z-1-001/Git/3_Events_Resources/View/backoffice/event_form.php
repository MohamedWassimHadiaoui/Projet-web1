<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/eventController.php";
$ec = new EventController();

$event = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $event = $ec->getEventById($_GET['id']);
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
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Event - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map { height: 200px; border-radius: 8px; margin-top: 0.5rem; }
        .calendar-input { position: relative; }
        .calendar-wrapper { position: absolute; top: 100%; left: 0; z-index: 1000; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; box-shadow: 0 10px 30px var(--shadow); display: none; min-width: 280px; }
        .calendar-wrapper.show { display: block; }
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .cal-header button { background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-primary); padding: 0.25rem 0.5rem; }
        .cal-header span { font-weight: 600; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.25rem; text-align: center; }
        .cal-day-name { font-size: 0.7rem; font-weight: 600; color: var(--text-muted); padding: 0.25rem; }
        .cal-day { padding: 0.5rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
        .cal-day:hover { background: rgba(99,102,241,0.2); }
        .cal-day.today { border: 2px solid var(--primary); }
        .cal-day.selected { background: var(--primary); color: white; }
        .cal-day.other-month { opacity: 0.3; }
        .time-row { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .time-row select { flex: 1; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit Event' : 'Add Event' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="eventForm" action="../../Controller/eventController.php" method="POST" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $event['id'] ?>"><?php endif; ?>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Event title" value="<?= htmlspecialchars($event['title'] ?? $old['title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Event description"><?= htmlspecialchars($event['description'] ?? $old['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group calendar-input">
                                <label>Date & Time</label>
                                <input type="text" id="dateDisplay" class="form-control" placeholder="Click to select date" readonly>
                                <input type="hidden" name="date_event" id="dateEvent" value="<?= $event ? date('Y-m-d\TH:i', strtotime($event['date_event'])) : ($old['date_event'] ?? '') ?>">
                                <div class="calendar-wrapper" id="calendarWrapper">
                                    <div class="cal-header">
                                        <button type="button" id="prevMonth">&lt;</button>
                                        <span id="calMonthYear"></span>
                                        <button type="button" id="nextMonth">&gt;</button>
                                    </div>
                                    <div class="cal-grid" id="calendarGrid"></div>
                                    <div class="time-row">
                                        <select id="hourSelect" class="form-control">
                                            <?php for($h=0;$h<24;$h++): ?><option value="<?= sprintf('%02d',$h) ?>"><?= sprintf('%02d',$h) ?></option><?php endfor; ?>
                                        </select>
                                        <span style="padding:0.5rem">:</span>
                                        <select id="minuteSelect" class="form-control">
                                            <?php for($m=0;$m<60;$m+=5): ?><option value="<?= sprintf('%02d',$m) ?>"><?= sprintf('%02d',$m) ?></option><?php endfor; ?>
                                        </select>
                                        <button type="button" id="confirmDateTime" class="btn btn-primary btn-sm">OK</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="online" <?= (($event['type'] ?? $old['type'] ?? '') === 'online') ? 'selected' : '' ?>>Online</option>
                                    <option value="offline" <?= (($event['type'] ?? $old['type'] ?? '') === 'offline') ? 'selected' : '' ?>>On-Site</option>
                                    <option value="hybrid" <?= (($event['type'] ?? $old['type'] ?? '') === 'hybrid') ? 'selected' : '' ?>>Hybrid</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Click on map or enter location" value="<?= htmlspecialchars($event['location'] ?? $old['location'] ?? '') ?>">
                            <div id="map"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Max Participants</label>
                                <input type="text" name="participants" class="form-control" placeholder="0" value="<?= htmlspecialchars($event['participants'] ?? $old['participants'] ?? '0') ?>">
                            </div>
                            <div class="form-group">
                                <label>Tags</label>
                                <input type="text" name="tags" class="form-control" placeholder="#workshop, #mediation" value="<?= htmlspecialchars($event['tags'] ?? $old['tags'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="events.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Map
        const map = L.map('map').setView([36.8, 10.18], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '(c) OpenStreetMap' }).addTo(map);
        let marker;
        map.on('click', e => {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('location').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
        });

        // Custom Calendar
        let currentDate = new Date();
        let selectedDate = null;
        const calendarWrapper = document.getElementById('calendarWrapper');
        const dateDisplay = document.getElementById('dateDisplay');
        const dateEvent = document.getElementById('dateEvent');
        const calMonthYear = document.getElementById('calMonthYear');
        const calendarGrid = document.getElementById('calendarGrid');

        // Initialize from existing value
        if (dateEvent.value) {
            selectedDate = new Date(dateEvent.value);
            currentDate = new Date(selectedDate);
            document.getElementById('hourSelect').value = String(selectedDate.getHours()).padStart(2, '0');
            document.getElementById('minuteSelect').value = String(Math.floor(selectedDate.getMinutes() / 5) * 5).padStart(2, '0');
            updateDateDisplay();
        }

        dateDisplay.addEventListener('click', () => {
            calendarWrapper.classList.toggle('show');
            renderCalendar();
        });

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        document.getElementById('confirmDateTime').addEventListener('click', () => {
            if (selectedDate) {
                selectedDate.setHours(parseInt(document.getElementById('hourSelect').value));
                selectedDate.setMinutes(parseInt(document.getElementById('minuteSelect').value));
                updateDateDisplay();
                calendarWrapper.classList.remove('show');
            }
        });

        function pad2(n) { return String(n).padStart(2, '0'); }
        function toLocalDateTimeValue(d) {
            return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}T${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
        }

        function updateDateDisplay() {
            if (selectedDate) {
                const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                dateDisplay.value = selectedDate.toLocaleString('en-US', options);
                dateEvent.value = toLocalDateTimeValue(selectedDate);
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            calMonthYear.textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            let html = '<div class="cal-day-name">Su</div><div class="cal-day-name">Mo</div><div class="cal-day-name">Tu</div><div class="cal-day-name">We</div><div class="cal-day-name">Th</div><div class="cal-day-name">Fr</div><div class="cal-day-name">Sa</div>';

            for (let i = 0; i < firstDay; i++) {
                html += '<div class="cal-day other-month"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
                const isSelected = selectedDate && selectedDate.getDate() === day && selectedDate.getMonth() === month && selectedDate.getFullYear() === year;
                html += `<div class="cal-day ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}" data-day="${day}">${day}</div>`;
            }

            calendarGrid.innerHTML = html;

            calendarGrid.querySelectorAll('.cal-day[data-day]').forEach(el => {
                el.addEventListener('click', () => {
                    selectedDate = new Date(year, month, parseInt(el.dataset.day));
                    renderCalendar();
                });
            });
        }

        // Close calendar when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.calendar-input')) {
                calendarWrapper.classList.remove('show');
            }
        });

        // Validation
        Validator.init('eventForm', {
            'title': [
                { type: 'required', message: 'Title is required' },
                { type: 'minLength', min: 5, message: 'Title must be at least 5 characters' }
            ],
            'description': [
                { type: 'required', message: 'Description is required' }
            ],
            'location': [
                { type: 'required', message: 'Location is required' }
            ]
        });
    </script>
</body>
</html>

