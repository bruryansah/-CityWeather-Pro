<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CityWeather Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-500 via-blue-600 to-purple-700 min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-2xl mx-auto text-white">
        
        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-center mb-4 sm:mb-6 fade-in">
            ☀️ CityWeather Pro
        </h1>

        <!-- Search box -->
        <div class="bg-white/20 p-4 sm:p-6 rounded-2xl backdrop-blur-md shadow-2xl border border-white/30 fade-in">
            <input id="cityInput"
                   class="w-full p-3 sm:p-4 rounded-xl text-black text-base sm:text-lg focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all"
                   placeholder="🔍 Enter city name..."
                   onkeypress="if(event.key==='Enter') searchWeather()">

            <button onclick="searchWeather()"
                    class="mt-3 w-full bg-white text-blue-700 font-bold p-3 sm:p-4 rounded-xl hover:bg-blue-50 hover:scale-105 transition-all duration-300 shadow-lg text-base sm:text-lg">
                🌤️ Search Weather
            </button>
            
            <button id="addFavorite" 
                    class="mt-3 w-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold p-3 sm:p-4 rounded-xl hover:from-yellow-500 hover:to-orange-600 hover:scale-105 transition-all duration-300 shadow-lg text-base sm:text-lg">
                ⭐ Add to Favorites
            </button>
            
            <canvas id="tempChart" class="mt-6 w-full"></canvas>

            <h3 class="mt-6 text-lg sm:text-xl font-bold flex items-center">
                <span class="mr-2">❤️</span> Favorite Cities:
            </h3>
            <ul id="favoriteList" class="list-none pl-0 mt-3 space-y-2"></ul>
        </div>

        <!-- Weather Result -->
        <div id="result" class="mt-4 sm:mt-6"></div>
    </div>

<script>
let chartInstance = null;

function renderChart(forecastData) {
    const labels = [];
    const temps = [];
    forecastData.list.forEach((item, index) => {
        if (index % 8 === 0) {
            labels.push(item.dt_txt.split(' ')[0]);
            temps.push(item.main.temp);
        }
    });

    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(document.getElementById('tempChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperature °C',
                data: temps,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: { color: 'white', font: { size: 12 } }
                }
            },
            scales: {
                y: {
                    ticks: { color: 'white' },
                    grid: { color: 'rgba(255, 255, 255, 0.2)' }
                },
                x: {
                    ticks: { color: 'white', font: { size: 10 } },
                    grid: { color: 'rgba(255, 255, 255, 0.2)' }
                }
            }
        }
    });
}

async function searchWeather() {
    const city = document.getElementById('cityInput').value;
    if (!city) return alert("Enter a city");

    const res = await fetch(`/search-weather?city=${city}`);
    const data = await res.json();

    if (data.error) {
        document.getElementById('result').innerHTML = `
            <div class="bg-red-500/90 backdrop-blur-md p-4 sm:p-6 mt-4 rounded-xl fade-in shadow-lg border border-red-300">
                <p class="font-semibold text-base sm:text-lg">❌ ${data.error}</p>
            </div>
        `;
        return;
    }

    document.getElementById('result').innerHTML = `
        <div class="bg-white/20 backdrop-blur-xl p-4 sm:p-6 rounded-2xl mt-4 shadow-2xl border border-white/30 fade-in">
            <div class="text-center mb-4">
                <h2 class="text-2xl sm:text-3xl font-bold mb-2">${data.name}</h2>
                <p class="text-4xl sm:text-5xl font-bold mb-1">${data.main.temp}°C</p>
                <p class="text-lg sm:text-xl text-blue-100 capitalize">${data.weather[0].description}</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mt-4">
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">🌡️</p>
                    <p class="text-xs sm:text-sm text-blue-100">Feels Like</p>
                    <p class="font-bold text-base sm:text-lg">${data.main.feels_like}°C</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">💧</p>
                    <p class="text-xs sm:text-sm text-blue-100">Humidity</p>
                    <p class="font-bold text-base sm:text-lg">${data.main.humidity}%</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">💨</p>
                    <p class="text-xs sm:text-sm text-blue-100">Wind Speed</p>
                    <p class="font-bold text-base sm:text-lg">${data.wind.speed} m/s</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">👁️</p>
                    <p class="text-xs sm:text-sm text-blue-100">Visibility</p>
                    <p class="font-bold text-base sm:text-lg">${(data.visibility / 1000).toFixed(1)} km</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">🌅</p>
                    <p class="text-xs sm:text-sm text-blue-100">Pressure</p>
                    <p class="font-bold text-base sm:text-lg">${data.main.pressure} hPa</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md p-3 sm:p-4 rounded-xl text-center">
                    <p class="text-2xl mb-1">☁️</p>
                    <p class="text-xs sm:text-sm text-blue-100">Cloudiness</p>
                    <p class="font-bold text-base sm:text-lg">${data.clouds.all}%</p>
                </div>
            </div>
        </div>
    `;

    getForecast(city);
    getAIRecommendation(data.weather[0].main);
}

async function getForecast(city) {
    const res = await fetch(`/forecast?city=${city}`);
    const data = await res.json();
    if (data.error) return;

    renderChart(data);

    let html = '<div class="bg-white/20 backdrop-blur-xl p-4 sm:p-6 rounded-2xl mt-4 shadow-2xl border border-white/30 fade-in">';
    html += '<h3 class="text-lg sm:text-xl font-bold mb-3">📊 5-Day Forecast:</h3>';
    html += '<div class="space-y-2">';
    
    data.list.forEach((item, index) => {
        if (index % 8 === 0) {
            html += `
                <div class="bg-white/20 backdrop-blur-md p-3 rounded-xl flex justify-between items-center">
                    <span class="text-sm sm:text-base font-semibold">${item.dt_txt}</span>
                    <span class="text-sm sm:text-base"><b>${item.main.temp}°C</b> - ${item.weather[0].description}</span>
                </div>
            `;
        }
    });
    
    html += '</div></div>';
    document.getElementById('result').innerHTML += html;
}

async function getAIRecommendation(weatherMain) {
    const res = await fetch('/ai-recommendation', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ weather: weatherMain })
    });
    const data = await res.json();
    
    document.getElementById('result').innerHTML += `
        <div class="bg-gradient-to-r from-purple-500/30 to-pink-500/30 backdrop-blur-xl p-4 sm:p-6 rounded-2xl mt-4 shadow-2xl border border-purple-300/50 fade-in">
            <p class="text-base sm:text-lg font-semibold">🤖 AI Advice: ${data.recommendation}</p>
        </div>
    `;
}

const favorites = JSON.parse(localStorage.getItem('favoriteCities')) || [];

function renderFavorites() {
    const list = document.getElementById('favoriteList');
    list.innerHTML = '';
    
    if (favorites.length === 0) {
        list.innerHTML = '<li class="text-blue-100 text-sm sm:text-base italic">No favorites yet.</li>';
        return;
    }
    
    favorites.forEach(city => {
        const li = document.createElement('li');
        li.className = 'bg-white/30 backdrop-blur-md p-2 sm:p-3 rounded-lg hover:bg-white/40 transition-all cursor-pointer text-sm sm:text-base font-semibold';
        li.textContent = '📍 ' + city;
        li.onclick = () => {
            document.getElementById('cityInput').value = city;
            searchWeather();
        };
        list.appendChild(li);
    });
}

document.getElementById('addFavorite').addEventListener('click', () => {
    const city = document.getElementById('cityInput').value;
    if (!city) return;
    if (!favorites.includes(city)) {
        favorites.push(city);
        localStorage.setItem('favoriteCities', JSON.stringify(favorites));
        renderFavorites();
    }
});

renderFavorites();
</script>

</body>
</html>