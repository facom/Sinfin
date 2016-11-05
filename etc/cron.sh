#!/bin/bash
if [ $1 = "daily" ]
then
    links -dump "http://astronomia-udea.co/principal/Sinfin/cron.php?action=comaca&daily"
else
    links -dump "http://astronomia-udea.co/principal/Sinfin/cron.php?action=comaca&weekly"
fi
