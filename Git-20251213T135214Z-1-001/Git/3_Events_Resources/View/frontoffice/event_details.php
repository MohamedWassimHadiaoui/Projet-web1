<?php
session_start();
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/eventController.php";

$ec = new EventController();
$id = (int)($_GET['id'] ?? 0);
$event = $id ? $ec->getEventById($id) : null;
if (!$event) {
    header("Location: " . $frontoffice . "events.php");
    exit;
}

$typeLabels = ['online' => '🌐 Virtual', 'offline' => '📍 On-Site', 'hybrid' => '🔄 Hybrid'];
$eventType = $event['type'] ?? '';
$isOnline = strtolower($eventType) === 'online';

$userId = $_SESSION['user_id'] ?? null;
$isSubscribed = $userId ? $ec->isUserSubscribed($id, $userId) : false;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title'] ?? 'Event') ?> - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .meta { display:flex; gap:1rem; flex-wrap:wrap; color:var(--text-muted); margin-top:0.5rem; }
        .meta span { display:flex; align-items:center; gap:0.35rem; }
        .weather-box {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(59,130,246,0.1));
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }
        .weather-icon { font-size: 3rem; margin-bottom: 0.5rem; }
        .weather-temp { font-size: 2.5rem; font-weight: 800; color: var(--text-primary); }
        .weather-desc { font-size: 1.1rem; color: var(--text-primary); margin: 0.5rem 0; }
        .weather-details { display: flex; justify-content: center; gap: 2rem; margin-top: 1rem; color: var(--text-muted); }
        .weather-location { margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem; }
        .weather-loading { color: var(--text-muted); padding: 2rem; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <a class="btn btn-secondary" href="<?= $frontoffice ?>events.php">Back</a>

            <div class="hero" style="margin-top:1rem">
                <h1><?= htmlspecialchars($event['title'] ?? '') ?></h1>
                <p><?= htmlspecialchars($event['description'] ?? '') ?></p>
                <div class="meta">
                    <?php if (!empty($event['date_event'])): ?>
                    <span>📅 <?= htmlspecialchars(date('M d, Y H:i', strtotime($event['date_event']))) ?></span>
                    <?php endif; ?>
                    <span class="badge badge-assigned"><?= $typeLabels[$event['type']] ?? htmlspecialchars($event['type'] ?? '') ?></span>
                    <?php if (!empty($event['location'])): ?>
                    <span>📍 <?= htmlspecialchars($event['location']) ?></span>
                    <?php endif; ?>
                    <span>👥 <?= (int)($event['participants'] ?? 0) ?> participants</span>
                </div>
            </div>

            <?php if (!$isOnline && !empty($event['location'])): ?>
            <div id="weather-section" style="margin: 1.5rem 0;">
                <h2 class="section-title">🌤️ Weather Forecast</h2>
                <div id="weather-box" class="weather-box">
                    <div class="weather-loading">Loading weather...</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom:1rem"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Details</h2>
                    <div style="color:var(--text-muted);line-height:1.7">
                        <?= nl2br(htmlspecialchars($event['description'] ?? '')) ?>
                    </div>
                    <?php if (!empty($event['tags'])): ?>
                        <div style="margin-top:1rem">
                            <?php foreach (explode(',', $event['tags']) as $t): $t = trim($t); if ($t === '') continue; ?>
                                <span class="badge badge-low"><?= htmlspecialchars($t) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card" style="margin-top:1.5rem">
                <div class="card-body">
                    <h2 class="card-title">📝 Register for this Event</h2>
                    <?php if ($isSubscribed): ?>
                        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                            <span style="color:var(--success);font-weight:600">✅ You are registered for this event!</span>
                            <form action="<?= $controller ?>eventController.php" method="POST" style="display:inline">
                                <input type="hidden" name="action" value="unsubscribe">
                                <input type="hidden" name="event_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to unsubscribe?')">Cancel Registration</button>
                            </form>
                        </div>
                    <?php elseif ($userId): ?>
                        <form action="<?= $controller ?>eventController.php" method="POST">
                            <input type="hidden" name="action" value="subscribe">
                            <input type="hidden" name="event_id" value="<?= $id ?>">
                            <p style="color:var(--text-muted);margin-bottom:1rem">Click below to register for this event. We'll save your spot!</p>
                            <button type="submit" class="btn btn-primary">🎟️ Register Now</button>
                        </form>
                    <?php else: ?>
                        <div id="guest-register">
                            <p style="color:var(--text-muted);margin-bottom:1rem">Enter your details to register for this event:</p>
                            <form id="guestForm" action="<?= $controller ?>eventController.php" method="POST">
                                <input type="hidden" name="action" value="subscribe">
                                <input type="hidden" name="event_id" value="<?= $id ?>">
                                <div class="form-group" style="margin-bottom:1rem">
                                    <label>Your Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter your name">
                                </div>
                                <div class="form-group" style="margin-bottom:1rem">
                                    <label>Email Address</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter your email">
                                </div>
                                <button type="submit" class="btn btn-primary">🎟️ Register Now</button>
                            </form>
                            <p style="color:var(--text-muted);margin-top:1rem;font-size:0.9rem">
                                Already have an account? <a href="<?= $frontoffice ?>login.php">Login</a> for easier registration.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    
    <?php if (!$isOnline && !empty($event['location'])): ?>
    <script>
    (function() {
        const eventDate = <?= json_encode($event['date_event'] ?? '') ?>;
        const eventLocation = <?= json_encode($event['location'] ?? '') ?>;
        const weatherBox = document.getElementById('weather-box');
        
        if (!weatherBox || !eventDate || !eventLocation) return;
        
        // Tunisian cities coordinates
        const cities = {
            'tunis': { lat: 36.8065, lon: 10.1815, name: 'Tunis' },
            'sfax': { lat: 34.7406, lon: 10.7603, name: 'Sfax' },
            'sousse': { lat: 35.8288, lon: 10.6405, name: 'Sousse' },
            'monastir': { lat: 35.7643, lon: 10.8113, name: 'Monastir' },
            'kairouan': { lat: 35.6781, lon: 10.0963, name: 'Kairouan' },
            'bizerte': { lat: 37.2744, lon: 9.8739, name: 'Bizerte' },
            'gabes': { lat: 33.8815, lon: 10.0982, name: 'Gabes' },
            'nabeul': { lat: 36.4513, lon: 10.7351, name: 'Nabeul' },
            'hammamet': { lat: 36.4000, lon: 10.6167, name: 'Hammamet' },
            'djerba': { lat: 33.8076, lon: 10.8451, name: 'Djerba' },
            'tozeur': { lat: 33.9197, lon: 8.1339, name: 'Tozeur' },
            'mahdia': { lat: 35.5047, lon: 11.0622, name: 'Mahdia' }
        };
        
        // Find city in location
        let cityData = null;
        const locLower = eventLocation.toLowerCase();
        for (const [key, data] of Object.entries(cities)) {
            if (locLower.includes(key)) {
                cityData = data;
                break;
            }
        }
        
        if (!cityData) {
            // Default to Tunis if city not found
            cityData = cities.tunis;
        }
        
        // Get weather icon
        function getWeatherInfo(code) {
            if (code === 0) return { icon: '☀️', desc: 'Clear sky' };
            if (code <= 3) return { icon: '⛅', desc: 'Partly cloudy' };
            if (code <= 48) return { icon: '🌫️', desc: 'Foggy' };
            if (code <= 67) return { icon: '🌧️', desc: 'Rainy' };
            if (code <= 77) return { icon: '❄️', desc: 'Snowy' };
            if (code >= 95) return { icon: '⛈️', desc: 'Thunderstorm' };
            return { icon: '🌤️', desc: 'Variable' };
        }
        
        // Parse date
        const dateStr = eventDate.split(' ')[0].split('T')[0];
        const eventDateObj = new Date(dateStr);
        const today = new Date();
        today.setHours(0,0,0,0);
        const diffDays = Math.ceil((eventDateObj - today) / (1000 * 60 * 60 * 24));
        
        // Check if date is valid for forecast
        if (diffDays < 0) {
            weatherBox.innerHTML = `
                <div class="weather-icon">📅</div>
                <div class="weather-desc">This event has already passed</div>
                <div class="weather-location">📍 ${cityData.name}, Tunisia</div>
            `;
            return;
        }
        
        if (diffDays > 14) {
            weatherBox.innerHTML = `
                <div class="weather-icon">🔮</div>
                <div class="weather-desc">Weather forecast available ${diffDays - 14} days before event</div>
                <div class="weather-location">📍 ${cityData.name}, Tunisia</div>
            `;
            return;
        }
        
        // Fetch weather from Open-Meteo (free, no API key needed)
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${cityData.lat}&longitude=${cityData.lon}&daily=weathercode,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${dateStr}&end_date=${dateStr}`;
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.daily && data.daily.weathercode && data.daily.weathercode.length > 0) {
                    const code = data.daily.weathercode[0];
                    const maxTemp = Math.round(data.daily.temperature_2m_max[0]);
                    const minTemp = Math.round(data.daily.temperature_2m_min[0]);
                    const avgTemp = Math.round((maxTemp + minTemp) / 2);
                    const weather = getWeatherInfo(code);
                    
                    weatherBox.innerHTML = `
                        <div class="weather-icon">${weather.icon}</div>
                        <div class="weather-temp">${avgTemp}°C</div>
                        <div class="weather-desc">${weather.desc}</div>
                        <div class="weather-details">
                            <span>🌡️ Min: ${minTemp}°C</span>
                            <span>🌡️ Max: ${maxTemp}°C</span>
                        </div>
                        <div class="weather-location">📍 ${cityData.name}, Tunisia 🇹🇳</div>
                    `;
                } else {
                    throw new Error('No data');
                }
            })
            .catch(err => {
                console.error('Weather error:', err);
                weatherBox.innerHTML = `
                    <div class="weather-icon">📍</div>
                    <div class="weather-desc">${cityData.name}, Tunisia</div>
                    <div class="weather-location">Weather data temporarily unavailable</div>
                `;
            });
    })();
    </script>
    <?php endif; ?>
</body>
</html>
