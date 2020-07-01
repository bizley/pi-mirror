const html = (id, body) => document.getElementById(id).innerHTML = body;
const dayName = dayNumber => {
    switch(dayNumber) {
        case 0: return 'niedziela';
        case 1: return 'poniedziałek';
        case 2: return 'wtorek';
        case 3: return 'środa';
        case 4: return 'czwartek';
        case 5: return 'piątek';
        case 6: return 'sobota';
    }
    return null;
}
const monthName = monthNumber => {
    switch(monthNumber) {
        case 1: return ' stycznia ';
        case 2: return ' lutego ';
        case 3: return ' marca ';
        case 4: return ' kwietnia ';
        case 5: return ' maja ';
        case 6: return ' czerwca ';
        case 7: return ' lipca ';
        case 8: return ' sierpnia ';
        case 9: return ' września ';
        case 10: return ' października ';
        case 11: return ' listopada ';
        case 12: return ' grudnia ';
    }
    return null;
}
const clock = () => {
    const date = new Date()
    const seconds = date.getSeconds()
    const hours = date.getHours()
    const minutes = date.getMinutes()
    const year = date.getFullYear()
    const month = date.getMonth() + 1
    const day = date.getDate()
    const dayNumber = date.getUTCDay()
    html('hours', (hours < 10 ? '0' : '') + hours)
    html('minutes', (minutes < 10 ? '0' : '') + minutes)
    html('colon', seconds % 2 ? '' : ':')
    html('dateString', dayName(dayNumber) + ', ' + day + monthName(month) + year)
}
const displayWeather = data => {
    let weather = '';
    if (data.current.weatherId !== null) {
        weather += '<span class="wi wi-owm-' + data.current.period + '-' + data.current.weatherId + '"></span>'
    }
    let degrees = '';
    if (data.current.temperature !== null) {
        degrees += data.current.temperature + '<span class="wi wi-degrees"></span>'
    }
    if (data.current.feelsLike !== null) {
        degrees += '<span class="smaller">' + data.current.feelsLike + '<span class="wi wi-degrees"></span></span>'
    }
    if (degrees !== '') {
        degrees = '<div>' + degrees + '</div>'
    }
    html('current', weather + degrees)
    let sunTimes = '';
    if (data.current.sunrise !== null) {
        sunTimes += ' <span class="wi wi-sunrise"></span> ' + data.current.sunrise
    }
    if (data.current.sunset !== null) {
        sunTimes += ' <span class="wi wi-sunset"></span> ' + data.current.sunset
    }
    html('sun', sunTimes)
    let extra = '';
    if (data.current.pressure !== null) {
        extra += '<div class="icon-column"><span class="wi wi-barometer"></span></div>' + data.current.pressure + ' hPa<br>';
    }
    if (data.current.windDirection !== null && data.current.windSpeed !== null) {
        extra += '<div class="icon-column"><span class="wi wi-wind from-' + data.current.windDirection + '-deg"></span></div>' + data.current.windSpeed + ' m/s<br>';
    }
    if (data.current.cloudiness !== null) {
        extra += '<div class="icon-column"><span class="wi wi-cloudy"></span></div>' + data.current.cloudiness + '%<br>';
    }
    if (data.current.humidity !== null) {
        extra += '<div class="icon-column"><span class="wi wi-humidity"></span></div>' + data.current.humidity + '%<br>';
    }
    if (data.current.rain !== null) {
        extra += '<div class="icon-column"><span class="wi wi-rain"></span></div>' + data.current.rain + ' mm<br>';
    }
    if (data.current.snow !== null) {
        extra += '<div class="icon-column"><span class="wi wi-snow"></span></div>' + data.current.snow + ' mm<br>';
    }
    html('extra', extra)
    const forecastHourly = data.hourly.map(item => {
        return '<li><span class="smaller">' + item.time
            + '</span><br><span class="wi wi-owm-' + item.weatherId + '"></span> '
            + item.temperature + '<span class="wi wi-degrees"></span>'
            + '<span class="smaller">' + item.feelsLike + '<span class="wi wi-degrees"></span></span>'
            + '</li>'
    })
    const forecastDaily = data.daily.map(item => {
        return '<li><span class="smaller">' + item.date
            + '</span><br><span class="wi wi-owm-' + item.weatherId + '"></span> <span class="smaller">'
            + item.weatherDescription + '</span><br>'
            + '<table><tr>'
            + '<td>' + item.morningTemperature + '<span class="wi wi-degrees"></span></td>'
            + '<td>' + item.dayTemperature + '<span class="wi wi-degrees"></span></td>'
            + '<td>' + item.eveningTemperature + '<span class="wi wi-degrees"></span></td>'
            + '<td>' + item.nightTemperature + '<span class="wi wi-degrees"></span></td>'
            + '</tr><tr>'
            + '<td class="smaller">' + item.morningFeelsLike + '<span class="wi wi-degrees"></span></td>'
            + '<td class="smaller">' + item.dayFeelsLike + '<span class="wi wi-degrees"></span></td>'
            + '<td class="smaller">' + item.eveningFeelsLike + '<span class="wi wi-degrees"></span></td>'
            + '<td class="smaller">' + item.nightFeelsLike + '<span class="wi wi-degrees"></span></td>'
            + '</tr></table>'
            + '</li>'
    })
    html('forecast', forecastHourly.join('') + forecastDaily.join(''))
    document.getElementById('line').style.display = 'block';
}
const weather = () => {
    fetch('/site/data')
        .then(response => response.json())
        .then(data => displayWeather(data))
        .catch(err => console.error("Something went wrong!", err));
}
weather()
window.setInterval(clock, 1000)
window.setInterval(weather, 60000)
