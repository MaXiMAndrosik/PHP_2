<?php

$address = 'home/reminder-bot/HomeWork1/Systemd/mysystemd.log';
$date = 'Сервис запущен ' . date('d M Y H:i:s'). PHP_EOL;
$fileHandler = fopen($address, 'a');
fwrite($fileHandler, $date);
fclose($fileHandler);
