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
