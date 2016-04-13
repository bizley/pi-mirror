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
window.setInterval(clock, 1000);