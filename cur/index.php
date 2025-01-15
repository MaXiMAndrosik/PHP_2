<?php

// echo date("i").PHP_EOL;

// echo date("H").PHP_EOL;

// echo date("d").PHP_EOL;

// echo date("m").PHP_EOL;

// echo date("w").PHP_EOL;

// file_put_contents('cache.txt', time());

sleep(20);

if (file_exists('cache.txt')) {
    $lastData = (int)file_get_contents('cache.txt');
} else {
    $lastData = time();
    file_put_contents('cache.txt', $lastData);
}

while (true) {
    if ($lastData === time()) {
        sleep(10);
        continue;
    }

    echo "Date: ". time() . PHP_EOL;

    $lastData = time();

    sleep(10);
}
