<?php

$pid = pcntl_fork();

if ($pid == -1) {
    die("Could not fork.");
} elseif ($pid) {
    exit();
} else {
    if (posix_setsid() == -1) {
        die("Could not set session id.");
    }

    chdir('/');

    fclose(STDIN) ;
    fclose(STDOUT) ;
    fclose (STDERR) ;

    $stdin = fopen('/dev/null', 'r');
    $stdout = fopen('/var/log/output.log', 'ab');
    $stderr = fopen('/var/log/error.log', 'ab');

    $data = 'Daemon запущен ' . date('d M Y H:i:s'). PHP_EOL;
    file_put_contents('/home/reminder-bot/HomeWork1/Supervisor/output.log', $data, FILE_APPEND | LOCK_EX);

    while (true) {
        $data = 'Демон в работе ' . date('d M Y H:i:s'). PHP_EOL;
        file_put_contents('/home/reminder-bot/HomeWork1/Supervisor/output.log', $data, FILE_APPEND | LOCK_EX);
        sleep(60);
    }
}