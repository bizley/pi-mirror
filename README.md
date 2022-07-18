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

### Docker Env

`docker-compose` is located in `./docker` folder.

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
@chromium-browser -kiosk -noerrdialogs --disable-session-crashed-bubble http://localhost:8000
```

Hide cursor:
```shell script
@unclutter -idle 0
```

Requires `unclutter` to be installed (`sudo apt-get install unclutter`).

### Motion:

```shell script
sudo apt-get install motion
```

Run:
```shell script
sudo systemctl enable motion
```

Verify with:
```shell script
sudo service motion status
```

Prepare a folder for images and videos:
```shell script
mkdir ~/motion
```

#### /etc/default/motion

```shell script
start_motion_daemon=yes
```

#### /etc/motion/motion.conf

```shell script
daemon on
width 800
height 600
output_pictures best
locate_motion_mode on
locate_motion_style cross
target_dir /home/pi/motion
stream_port 0
webcontrol_localhost off
on_picture_save mpack -s "Motion detected!" %f your@gmail.com
```

### Email alerts:

```shell script
sudo apt-get install ssmtp
sudo apt-get install mailutils
sudo apt-get install mpack
```

#### /etc/ssmtp/ssmtp.conf

```shell script
root=postmaster
mailhub=smtp.gmail.com:587
hostname=ras-pi
AuthUser=yourGmailUser@gmail.com
AuthPass=YourGmailPass
FromLineOverride=YES
UseSTARTTLS=YES
```

This may require switching less secured apps access on your Gmail account.

### Controlling Motion

Go to the URL: `http://your-pi-local-ip:8080/`

### PIR Sensor script for powering monitor

#### ~/pir.py

```python
import RPi.GPIO as GPIO
import time
import subprocess

SENSOR_PIN = 23
screenOn = 1
eventTime = time.time()

GPIO.setmode(GPIO.BCM)
GPIO.setup(SENSOR_PIN, GPIO.IN)

def powerOn(channel):
    global screenOn
    global eventTime
    eventTime = time.time()
    if screenOn == 0:
        subprocess.run(["vcgencmd", "display_power", "1"])
        screenOn = 1

try:
    GPIO.add_event_detect(SENSOR_PIN , GPIO.RISING, callback=powerOn)
    while True:
        time.sleep(10)
        if screenOn == 1 and time.time() - eventTime > 30:
            subprocess.run(["vcgencmd", "display_power", "0"])
            screenOn = 0
except KeyboardInterrupt:
    print("Motion detector switched off...")
GPIO.cleanup()
```

#### /etc/rc.local

Add before `Exit 0`
```shell script
python3 /home/pi/pir.py > /dev/null 2>&1 &
```
