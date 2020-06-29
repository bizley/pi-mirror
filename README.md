PI Mirror Display Interface
===========================

## Yii 2 config:

Create `./config/param-local.php` file with:

```php
<?php

return [
    'apiKey' => 'Open Weather Map API Key',
    'units' => 'metric', // *OWM
    'lang' => 'pl', // *OWM
    'cacheDuration' => 600, // Yii cache duration
    'latitude' => 0.000, // Your location latitude
    'longitude' => 0.000, // Your location longitude
];
```

`*OWM` = See https://openweathermap.org/api/one-call-api for options 

## Raspbian:

### /boot/config.txt

Disable camera LED:
```shell script
disable_camera_led=1
```

Rotate HDMI display 90 degrees:
```shell script
display_hdmi_rotate=1
```

### ~/.config/lxsession/LXDE-pi/autostart

Run Chromium in kiosk mode with Open Weather Map interface:
```shell script
@chromium-browser -kiosk http://localhost:8000
```

Hide cursor:
```shell script
@unclutter -idle 0
```

Requires `unclutter` to be installed (`sudo apt-get install unclutter`).
