<?php
$this->title = 'PI Mirror';

$resp = json_decode('{"coord":{"lon":17.03,"lat":51.1},"weather":[{"id":800,"main":"Clear","description":"Bezchmurnie","icon":"01n"}],"base":"cmc stations","main":{"temp":9,"pressure":1010,"humidity":87,"temp_min":9,"temp_max":9},"wind":{"speed":2.6,"deg":160},"clouds":{"all":0},"dt":1459969200,"sys":{"type":1,"id":5375,"message":0.0033,"country":"PL","sunrise":1459916042,"sunset":1459964093},"id":3081368,"name":"Wroclaw","cod":200}', true);
$period = 'night';
if (date('G') >= 6 && date('G') < 18) {
    $period = 'day';
}
?>

<div id="main">
    <div id="hours"></div>
    <div id="colon"></div>
    <div id="minutes"></div>
    <div id="weather"><span class="wi wi-owm-<?= $period . '-' . $resp['weather'][0]['id'] ?>"></span></div>
    <div id="degrees"><?= $resp['main']['temp'] ?><span class="deg">&deg;</span></div>
    <div id="date"><span id="year"></span> / <span id="month"></span> / <span id="day"></span></div>
</div>