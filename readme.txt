AT&T router/modem logging script.   

a PHP script to log your modem

Manufacturer	Pace Plc
Model	5268AC


Outdoor Antenna Information
Type	Version
Manufacturer	NetComm Wireless Limited
Model	IFWA661 Series


This is a script to monitor the att modem and log reboots and down events.
run in chron on a pi

I wrote this back when all the routers were rebooting every 13 hrs. 
Now Im using it to monitor what channel and band its on how long its been since a reboot signal levels and temp.

You run this on a PI under chron and it will build a log file. Be sure you dont it to often I run 3 times a day.
