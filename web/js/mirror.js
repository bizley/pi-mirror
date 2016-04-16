function clock() {
    var date  = new Date();
    var seconds = date.getSeconds();
    var hours   = date.getHours();
    var minutes = date.getMinutes();
    var year    = date.getFullYear();
    var month   = date.getMonth() + 1;
    var day     = date.getDate();
    document.getElementById('hours').innerHTML   = (hours < 10 ? '0' : '') + hours;
    document.getElementById('minutes').innerHTML = (minutes < 10 ? '0' : '') + minutes;
    document.getElementById('year').innerHTML    = year;
    document.getElementById('month').innerHTML   = (month < 10 ? '0' : '') + month;
    document.getElementById('day').innerHTML     = (day < 10 ? '0' : '') + day;
    document.getElementById('colon').innerHTML   = seconds % 2 ? '' : ':';
}
function weatherXhrComplete(event) {
    if (parseInt(event.target.readyState) !== 4) return;
    if (parseInt(event.target.status) === 200) {
        var response = JSON.parse(event.target.responseText);
        var weather = '';
        if (response.period !== null && response.weatherId !== null) {
            weather += '<span class="wi wi-owm-' + response.period + '-' + response.weatherId + '"></span> ';
        }
        if (response.temp !== null) {
            weather += response.temp + '<span class="wi wi-degrees"></span>';
        }
        document.getElementById('weather').innerHTML = weather;
        var extra = '';
        if (response.pressure !== null) {
            extra += '<div class="icon-column"><span class="wi wi-barometer"></span></div>' + response.pressure + ' hPa<br>';
        }
        if (response.windDeg !== null && response.windSpeed !== null) {
            extra += '<div class="icon-column"><span class="wi wi-wind from-' + response.windDeg + '-deg"></span></div>' + response.windSpeed + ' m/s<br>';
        }
        if (response.clouds !== null) {
            extra += '<div class="icon-column"><span class="wi wi-cloudy"></span></div>' + response.clouds + '%<br>';
        }
        if (response.humidity !== null) {
            extra += '<div class="icon-column"><span class="wi wi-raindrop"></span></div>' + response.humidity + '%<br>';
        }
        document.getElementById('weather-extra').innerHTML = extra;
    }
}
function weather() {
    var xhr = new XMLHttpRequest();
    xhr.addEventListener('readystatechange', weatherXhrComplete);
    xhr.open('GET', '/site/weather', true);
    xhr.send(null);
}
function forecastXhrComplete(event) {
    if (parseInt(event.target.readyState) !== 4) return;
    if (parseInt(event.target.status) === 200) {
        var response = JSON.parse(event.target.responseText);
        var forecast = '';
        var today    = '';
        if (response.today !== null) {
            for (var h in response.today) {
                today += '<div class="forecast-hour"><span class="wi wi-owm-' + response.today[h].weatherId + '"></span> ' + response.today[h].temp + '<span class="wi wi-degrees"></span><span class="hour">' + h + '</span></div>';
            }
        }
        if (today !== '') {
            forecast += '<div class="forecast-row"><div>DZISIAJ</div>' + today + '</div>';
        }
        var tomorrow = '';
        if (response.tomorrow !== null) {
            for (var h in response.tomorrow) {
                tomorrow += '<div class="forecast-hour"><span class="wi wi-owm-' + response.tomorrow[h].weatherId + '"></span> ' + response.tomorrow[h].temp + '<span class="wi wi-degrees"></span><span class="hour">' + h + '</span></div>';
            }
        }
        if (tomorrow !== '') {
            forecast += '<div class="forecast-row"><div>JUTRO</div>' + tomorrow + '</div>';
        }
        var overmorrow = '';
        if (response.overmorrow !== null) {
            for (var h in response.overmorrow) {
                overmorrow += '<div class="forecast-hour"><span class="wi wi-owm-' + response.overmorrow[h].weatherId + '"></span> ' + response.overmorrow[h].temp + '<span class="wi wi-degrees"></span><span class="hour">' + h + '</span></div>';
            }
        }
        if (overmorrow !== '') {
            forecast += '<div class="forecast-row"><div>POJUTRZE</div>' + overmorrow + '</div>';
        }
        document.getElementById('forecast').innerHTML = forecast;
    }
}
function forecast() {
    var xhr = new XMLHttpRequest();
    xhr.addEventListener('readystatechange', forecastXhrComplete);
    xhr.open('GET', '/site/forecast', true);
    xhr.send(null);
}
clock();
window.setInterval(clock, 1000);
weather();
window.setInterval(weather, 600000);
forecast();
window.setInterval(forecast, 1800000);
