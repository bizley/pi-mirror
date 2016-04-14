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
function xhrComplete(event) {
    if (parseInt(event.target.readyState) !== 4) return;
    if (parseInt(event.target.status) === 200) {
        var response = JSON.parse(event.target.responseText);
        document.getElementById('weather').innerHTML = '<span class="wi wi-owm-' + response.period + '-' + response.weatherId + '"></span> ' + response.temp + '<span class="deg">&deg;</span>';
    }
}
function weather() {
    var xhr = new XMLHttpRequest();
    xhr.addEventListener('readystatechange', xhrComplete);
    xhr.open('GET', '/site/weather', true);
    xhr.send(null);
}
clock();
window.setInterval(clock, 1000);
weather();
window.setInterval(weather, 600000);
