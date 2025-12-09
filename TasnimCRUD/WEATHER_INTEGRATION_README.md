# 🌦️ Weather Integration Guide

## 📋 Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Detailed Implementation](#detailed-implementation)
- [API Documentation](#api-documentation)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)
- [Examples](#examples)

---

## 🎯 Overview

This guide explains how to integrate **free weather forecasts** into any web application using the **Open-Meteo API**. The implementation is **client-side** (JavaScript), requires **no API key**, and works with any location and date.

### What it does:
- ✅ Displays weather forecast for a specific date and location
- ✅ Automatically hides weather for online/virtual events
- ✅ Shows temperature (min/max), weather condition with emoji icons
- ✅ Handles geocoding (converts city names to GPS coordinates)
- ✅ Beautiful gradient design with responsive layout

---

## 🌟 Features

| Feature | Description |
|---------|-------------|
| **Free API** | No API key required, unlimited requests |
| **Smart Detection** | Automatically hides weather for online events |
| **Geocoding** | Converts location names to coordinates |
| **Multi-language** | Supports French and other languages |
| **Error Handling** | Graceful fallbacks for missing data |
| **Modern UI** | Beautiful gradient design with emojis |
| **Up to 16 days** | Forecast available for next 16 days |

---

## 📦 Prerequisites

- Basic HTML/CSS/JavaScript knowledge
- A web page with event data (date and location)
- Modern browser with Fetch API support

**No backend required!** Everything runs in the browser.

---

## 🚀 Quick Start

### Step 1: Add HTML Container

```html
<!-- Weather Section -->
<div id="weather-wrapper" style="margin-bottom: 1.5rem;">
    <h2 style="margin-bottom: 1rem;">Météo le jour de l'événement</h2>
    <div id="weather-info" style="background: linear-gradient(135deg, #6dd5fa 0%, #2980b9 100%); 
         color: white; padding: 1.5rem; border-radius: 1rem; text-align: center; 
         box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <p><em>Chargement des prévisions...</em></p>
    </div>
</div>
```

### Step 2: Add JavaScript

```html
<script>
document.addEventListener('DOMContentLoaded', async () => {
    // YOUR EVENT DATA
    const eventDate = "2024-12-25";  // Format: YYYY-MM-DD
    const eventLocation = "Paris";   // City name or full address
    const eventType = "offline";     // "online", "offline", or "hybrid"
    
    const weatherContainer = document.getElementById('weather-info');
    const weatherWrapper = document.getElementById('weather-wrapper');

    // Validation
    if (!eventDate || !eventLocation || !weatherWrapper) {
        if(weatherWrapper) weatherWrapper.style.display = 'none';
        return;
    }

    // Hide weather for online events
    const onlineKeywords = ['zoom', 'en ligne', 'online', 'webinar', 'teams', 'google meet', 'discord', 'skype'];
    const isOnline = (eventType === 'online') || onlineKeywords.some(kw => eventLocation.toLowerCase().includes(kw));
    
    if (isOnline) {
        weatherWrapper.style.display = 'none';
        return;
    }

    // Weather code to description mapping
    const getWeatherDescription = (code) => {
        if (code === 0) return "☀️ Ciel dégagé";
        if (code >= 1 && code <= 3) return "⛅ Partiellement nuageux";
        if (code >= 45 && code <= 48) return "🌫️ Brouillard";
        if (code >= 51 && code <= 67) return "🌧️ Pluie";
        if (code >= 71 && code <= 77) return "❄️ Neige";
        if (code >= 95) return "⛈️ Orage";
        return "Météo variable";
    };

    // Geocoding function
    const findCityCoordinates = async (locationStr) => {
        // Attempt 1: Exact search
        let url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(locationStr)}&count=1&language=fr&format=json`;
        let req = await fetch(url);
        let data = await req.json();
        if (data.results && data.results.length > 0) return data.results[0];

        // Attempt 2: If comma exists, try first part (e.g., "Paris, France" -> "Paris")
        if (locationStr.includes(',')) {
            let city = locationStr.split(',')[0].trim();
            if (city.length > 2) {
                url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=fr&format=json`;
                req = await fetch(url);
                data = await req.json();
                if (data.results && data.results.length > 0) return data.results[0];
            }
        }
        
        return null;
    };

    try {
        // Get coordinates from location name
        const geoResult = await findCityCoordinates(eventLocation);
        
        if (!geoResult) {
            weatherContainer.innerHTML = '<p>Lieu non trouvé pour l\'affichage météo.</p>';
            return;
        }

        const { latitude, longitude, name } = geoResult;

        // Get weather data
        let targetDate = eventDate.split(' ')[0]; // Keep only date part (YYYY-MM-DD)
        
        const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=weathercode,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${targetDate}&end_date=${targetDate}`;
        
        const wReq = await fetch(weatherUrl);
        const weatherData = await wReq.json();

        if (!weatherData.daily || !weatherData.daily.time || weatherData.daily.time.length === 0) {
            weatherContainer.innerHTML = '<p>Données météo non disponibles pour cette date.</p>';
            return;
        }

        const maxTemp = weatherData.daily.temperature_2m_max[0];
        const minTemp = weatherData.daily.temperature_2m_min[0];
        const weatherCode = weatherData.daily.weathercode[0];
        const desc = getWeatherDescription(weatherCode);

        // Display weather
        weatherContainer.innerHTML = `
            <h3 style="margin:0; font-size: 1.5rem; color: white;">${desc}</h3>
            <div style="font-size: 3rem; margin: 10px 0; font-weight: bold;">
                ${Math.round((maxTemp + minTemp) / 2)}°C
            </div>
            <div style="display: flex; justify-content: center; gap: 20px; font-size: 1.1rem;">
                <span>🔽 ${minTemp}°C</span>
                <span>🔼 ${maxTemp}°C</span>
            </div>
            <p style="margin-top: 15px; font-size: 0.9rem; opacity: 0.9;">📍 ${name}</p>
        `;

    } catch (e) {
        console.error("Weather Error:", e);
        weatherContainer.innerHTML = '<p>Météo non disponible.</p>';
    }
});
</script>
```

---

## 🔧 Detailed Implementation

### For PHP Projects

If you're using PHP to generate your page:

```php
<!-- In your PHP file -->
<script>
document.addEventListener('DOMContentLoaded', async () => {
    // Inject PHP variables into JavaScript
    const eventDate = "<?php echo isset($event['date_event']) ? $event['date_event'] : ''; ?>"; 
    const eventLocation = "<?php echo isset($event['location']) ? htmlspecialchars($event['location']) : ''; ?>";
    const eventType = "<?php echo isset($event['type']) ? $event['type'] : ''; ?>";
    
    // ... rest of the JavaScript code from Quick Start
});
</script>
```

### For React/Vue/Angular

```javascript
// React Example
import { useEffect, useState } from 'react';

function WeatherWidget({ eventDate, eventLocation, eventType }) {
    const [weather, setWeather] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        async function fetchWeather() {
            // Use the same logic from Quick Start
            // Set weather state with the result
        }
        fetchWeather();
    }, [eventDate, eventLocation]);

    return (
        <div id="weather-wrapper">
            {/* Render weather data */}
        </div>
    );
}
```

### For Static HTML

```html
<script>
// Simply hardcode your values
const eventDate = "2024-12-25";
const eventLocation = "Paris";
const eventType = "offline";

// ... rest of the code
</script>
```

---

## 📚 API Documentation

### 1. Geocoding API

**Endpoint:** `https://geocoding-api.open-meteo.com/v1/search`

**Parameters:**
| Parameter | Required | Description | Example |
|-----------|----------|-------------|---------|
| `name` | Yes | City or location name | `Paris` |
| `count` | No | Number of results (default: 10) | `1` |
| `language` | No | Response language | `fr`, `en` |
| `format` | No | Response format | `json` |

**Response:**
```json
{
  "results": [
    {
      "id": 2988507,
      "name": "Paris",
      "latitude": 48.85341,
      "longitude": 2.3488,
      "country": "France",
      "admin1": "Île-de-France"
    }
  ]
}
```

### 2. Weather Forecast API

**Endpoint:** `https://api.open-meteo.com/v1/forecast`

**Parameters:**
| Parameter | Required | Description | Example |
|-----------|----------|-------------|---------|
| `latitude` | Yes | GPS latitude | `48.8534` |
| `longitude` | Yes | GPS longitude | `2.3488` |
| `daily` | Yes | Daily weather variables | `weathercode,temperature_2m_max,temperature_2m_min` |
| `start_date` | No | Start date (YYYY-MM-DD) | `2024-12-25` |
| `end_date` | No | End date (YYYY-MM-DD) | `2024-12-25` |
| `timezone` | No | Timezone | `auto`, `Europe/Paris` |

**Response:**
```json
{
  "daily": {
    "time": ["2024-12-25"],
    "weathercode": [0],
    "temperature_2m_max": [15.2],
    "temperature_2m_min": [8.5]
  }
}
```

### Weather Codes Reference

| Code Range | Condition | Emoji |
|------------|-----------|-------|
| 0 | Clear sky | ☀️ |
| 1-3 | Partly cloudy | ⛅ |
| 45-48 | Fog | 🌫️ |
| 51-67 | Rain | 🌧️ |
| 71-77 | Snow | ❄️ |
| 95+ | Thunderstorm | ⛈️ |

[Full WMO Weather Code List](https://open-meteo.com/en/docs)

---

## 🎨 Customization

### Change Colors

```css
/* Modify the gradient background */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Purple */
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); /* Pink */
background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); /* Cyan */
```

### Change Temperature Unit

```javascript
// For Fahrenheit
const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=weathercode,temperature_2m_max,temperature_2m_min&temperature_unit=fahrenheit&timezone=auto&start_date=${targetDate}&end_date=${targetDate}`;

// Display
${Math.round((maxTemp + minTemp) / 2)}°F
```

### Add More Weather Data

```javascript
// Request additional parameters
&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max

// Access in response
const precipitation = weatherData.daily.precipitation_sum[0];
const windSpeed = weatherData.daily.windspeed_10m_max[0];

// Display
<p>💧 Précipitations: ${precipitation}mm</p>
<p>💨 Vent: ${windSpeed}km/h</p>
```

### Custom Weather Descriptions

```javascript
const getWeatherDescription = (code) => {
    const descriptions = {
        0: "☀️ Sunny",
        1: "🌤️ Mainly Clear",
        2: "⛅ Partly Cloudy",
        3: "☁️ Overcast",
        45: "🌫️ Foggy",
        // Add more custom descriptions
    };
    return descriptions[code] || "Weather varies";
};
```

---

## 🐛 Troubleshooting

### Issue: "Lieu non trouvé"

**Cause:** Geocoding API couldn't find the location.

**Solutions:**
1. Use more specific location names (e.g., "Paris, France" instead of "Paris")
2. Check for typos in location name
3. Try major city names instead of small villages
4. Use coordinates directly if available

```javascript
// Skip geocoding if you have coordinates
const latitude = 48.8534;
const longitude = 2.3488;
// Proceed directly to weather API
```

### Issue: "Données météo non disponibles"

**Cause:** Date is too far in the future (>16 days) or API error.

**Solutions:**
1. Check date format is YYYY-MM-DD
2. Ensure date is within next 16 days
3. Check browser console for API errors

### Issue: Weather shows for online events

**Cause:** Event type not properly detected.

**Solution:**
```javascript
// Add more keywords
const onlineKeywords = ['zoom', 'en ligne', 'online', 'webinar', 'teams', 
                        'google meet', 'discord', 'skype', 'virtual', 
                        'remote', 'internet'];
```

### Issue: CORS errors

**Cause:** Some browsers block cross-origin requests.

**Solution:** Open-Meteo has CORS enabled, but if issues persist:
```javascript
// Use a CORS proxy (not recommended for production)
const proxyUrl = 'https://cors-anywhere.herokuapp.com/';
const weatherUrl = proxyUrl + `https://api.open-meteo.com/...`;
```

---

## 💡 Examples

### Example 1: Multiple Events

```html
<div class="event-card" data-date="2024-12-25" data-location="Paris">
    <h3>Christmas Event</h3>
    <div class="weather-container"></div>
</div>

<div class="event-card" data-date="2024-12-31" data-location="Lyon">
    <h3>New Year Event</h3>
    <div class="weather-container"></div>
</div>

<script>
document.querySelectorAll('.event-card').forEach(async (card) => {
    const date = card.dataset.date;
    const location = card.dataset.location;
    const container = card.querySelector('.weather-container');
    
    // Fetch and display weather for each event
    // ... use the same logic
});
</script>
```

### Example 2: 7-Day Forecast

```javascript
// Request 7 days instead of 1
const today = new Date();
const endDate = new Date(today);
endDate.setDate(today.getDate() + 7);

const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=weathercode,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${today.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`;

// Loop through results
weatherData.daily.time.forEach((date, index) => {
    const maxTemp = weatherData.daily.temperature_2m_max[index];
    const minTemp = weatherData.daily.temperature_2m_min[index];
    // Display each day
});
```

### Example 3: With Loading Spinner

```html
<div id="weather-info">
    <div class="spinner"></div>
    <p>Chargement...</p>
</div>

<style>
.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
```

---

## 📖 Best Practices

1. **Cache Results:** Store API responses in localStorage to reduce requests
   ```javascript
   const cacheKey = `weather_${latitude}_${longitude}_${targetDate}`;
   const cached = localStorage.getItem(cacheKey);
   if (cached) {
       const data = JSON.parse(cached);
       // Use cached data
   }
   ```

2. **Error Handling:** Always provide fallback UI
   ```javascript
   try {
       // API calls
   } catch (error) {
       console.error(error);
       weatherContainer.innerHTML = '<p>Météo temporairement indisponible</p>';
   }
   ```

3. **Performance:** Use async/await and don't block page load
   ```javascript
   document.addEventListener('DOMContentLoaded', async () => {
       // Weather code runs after page loads
   });
   ```

4. **Accessibility:** Add ARIA labels
   ```html
   <div id="weather-info" role="region" aria-label="Weather forecast">
   ```

---

## 🔗 Useful Links

- [Open-Meteo API Documentation](https://open-meteo.com/en/docs)
- [Geocoding API Docs](https://open-meteo.com/en/docs/geocoding-api)
- [Weather Codes Reference](https://www.nodc.noaa.gov/archive/arc0021/0002199/1.1/data/0-data/HTML/WMO-CODE/WMO4677.HTM)
- [Fetch API MDN](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

---

## 📝 License

This integration uses the **Open-Meteo API** which is free for non-commercial use.

For commercial use, please check: https://open-meteo.com/en/pricing

---

## 🤝 Contributing

Feel free to improve this guide! Suggestions:
- Add more language translations
- Create framework-specific examples (React, Vue, Angular)
- Add more weather parameters (humidity, UV index, etc.)
- Improve error handling

---

## ⭐ Credits

- **API Provider:** [Open-Meteo](https://open-meteo.com/)
- **Weather Data:** NOAA, DWD, Météo-France
- **Integration:** TasnimCRUD Project

---

**Happy Coding! 🚀**

If you use this in your project, consider giving credit or sharing your implementation!
