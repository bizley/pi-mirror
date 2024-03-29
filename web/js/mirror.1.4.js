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
    document.getElementById('offline').style.display = data.online ? 'none' : 'block';
    if (data.current === null) {
        html('current', '')
        html('sun', 'brak danych aktualnej pogody')
        html('extra', '')
    } else {
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
        extra += '<div class="icon-column">&nbsp;</div><span class="gray">' + data.lastDate + '</span>';
        html('extra', extra)
    }
    if (data.hourly === null || data.daily === null) {
        html('forecast', 'brak danych prognozy pogody')
    } else {
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
}
const displayMpk = data => {
    const vehicles = data.map(item => {
        return '<div class="" style="position:relative;top:' + item.position[0] + 'px;left:' + item.position[1] + 'px;color:#fff">' + item.nr + '</div>'
    })
    html('map', vehicles.join(''))
}
const weather = () => {
    fetch('/site/data')
        .then(response => response.json())
        .then(data => displayWeather(data))
        .catch(err => console.error("Something went wrong!", err));
}
weather()
const mpk = () => {
    fetch('/site/mpk')
        .then(response => response.json())
        .then(data => displayMpk(data))
        .catch(err => console.error("Something went wrong!", err));
}
mpk()
window.setInterval(clock, 1000)
window.setInterval(weather, 60000)
window.setInterval(mpk, 60000)
