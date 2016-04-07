<?php

$resp = json_decode('{"coord":{"lon":17.03,"lat":51.1},"weather":[{"id":800,"main":"Clear","description":"Bezchmurnie","icon":"01n"}],"base":"cmc stations","main":{"temp":9,"pressure":1010,"humidity":87,"temp_min":9,"temp_max":9},"wind":{"speed":2.6,"deg":160},"clouds":{"all":0},"dt":1459969200,"sys":{"type":1,"id":5375,"message":0.0033,"country":"PL","sunrise":1459916042,"sunset":1459964093},"id":3081368,"name":"Wroclaw","cod":200}', true);
$period = 'night';
if (date('G') >= 6 && date('G') < 18) {
    $period = 'day';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PI Mirror</title>
<link href="https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext" rel="stylesheet" type="text/css">
<link href="/css/style.css" rel="stylesheet">
<link href="/css/weather-icons.min.css" rel="stylesheet">
</head>
<body>
    <table>
        <tr>
            <td class="time time-hou"><?= date('H') ?></td>
            <td class="time time-sem">:</td>
            <td class="time time-min"><?= date('i') ?></td>
            <td class="weather">
                <span class="wi wi-owm-<?= $period . '-' . $resp['weather'][0]['id'] ?>"></span> <?= $resp['main']['temp'] ?><span class="deg">&deg;</span>
                <p class="date"><?= date('Y/m/d') ?></p>
            </td>
        </tr>
    </table>
</body>
</html>
