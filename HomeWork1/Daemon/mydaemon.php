<?php

declare(ticks=1);

function signalHandler($signal): void {
    switch ($signal) {
        case SIGTERM:
            // Обработка задач остановки
            file_put_contents('/home/reminder-bot/HomeWork1/Daemon/output.log', 'HANDLE SIGNAL ' . $signal . PHP_EOL, FILE_APPEND | LOCK_EX);
            exit;
        case SIGINT:
            // обработка CTRL+C
            file_put_contents('/home/reminder-bot/HomeWork1/Daemon/output.log', 'HANDLE SIGNAL ' . $signal . PHP_EOL, FILE_APPEND | LOCK_EX);
            break;
        case SIGHUP:
            // обработка задач перезапуска
            file_put_contents('/home/reminder-bot/HomeWork1/Daemon/output.log', 'HANDLE SIGNAL ' . $signal . PHP_EOL, FILE_APPEND | LOCK_EX);
            break;
        default:
            echo 'HANDLE SIGNAL ' . $signal . PHP_EOL;
    }
}

function isDaemonActive($pid_file) {
    if (is_file($pid_file)) {
        $pid = file_get_contents($pid_file);
        if (posix_kill($pid, 0)) {
            //демон уже запущен
            return true;
        } else {
            //pid-файл есть, но процесса нет
            if (!unlink($pid_file)) {
            exit(-1);
            }
        }
    }
    return false;
}

if (isDaemonActive('/tmp/my_pid_file.pid')) {
    echo 'Демон уже запущен';
    exit;
}

pcntl_signal(SIGTERM, "signalHandler");
pcntl_signal(SIGHUP, "signalHandler");
pcntl_signal(SIGINT, "signalHandler");

$pid = pcntl_fork();

if ($pid == -1) {
    die("Could not fork.");
} elseif ($pid) {
    exit();
} else {
    if (posix_setsid() == -1) {
        die("Could not set session id.");
    }
    file_put_contents('/tmp/my_pid_file.pid', getmypid());
    chdir('/');
    fclose(STDIN);
    fclose(STDOUT);
    fclose(STDERR);
    $stdin = fopen('/dev/null', 'r');
    $stdout = fopen('/path/to/output.log', 'ab');
    $stderr = fopen('/path/to/error.log', 'ab');

    $data = 'Daemon запущен ' . date('d M Y H:i:s'). PHP_EOL;
    file_put_contents('/home/reminder-bot/HomeWork1/Daemon/output.log', $data, FILE_APPEND | LOCK_EX);

    while (true) {
        // Полезный код
        $data = 'Демон в работе ' . date('d M Y H:i:s'). PHP_EOL;
        file_put_contents('/home/reminder-bot/HomeWork1/Daemon/output.log', $data, FILE_APPEND | LOCK_EX);
        sleep(60);
    }
}