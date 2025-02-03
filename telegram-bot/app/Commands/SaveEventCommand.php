<?php

namespace App\Commands;

use App\Application;
use App\Actions\CronValues;
use App\Actions\EventSaver;
use App\Models\Event;
use App\Database\SQLite;

//php runner -c save_event --name 'Имя события' --receiver 'Айди получателя, пока любой' --text 'Текст напоминания' --cron '* * * * *'
class SaveEventCommand extends Command {

    protected Application $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function run(array $options  = []): void {

        $options = $this->getOptionValues();

        if ($this->isNeedHelp($options)) {
            $this->showHelp();
            return;
        }

        $this->handleEvent($options);

    }

    public function handleEvent(array $options  = []): void {

        $cron = new CronValues();
        $cronValues = $cron->getCronValues($options['cron']);

        $params = [
            'name' => $options['name'],
            'text' => $options['text'],
            'receiver_id' => $options['receiver'],
            'minute' => $cronValues[0],
            'hour' => $cronValues[1],
            'day' => $cronValues[2],
            'month' => $cronValues[3],
            'weekday' => $cronValues[4]
        ];

        $eventModel = new Event(new SQLite($this->app));
        
        $eventSaver =  new EventSaver($eventModel);

        $eventSaver->handle($params);
    }

    public function getOptionValues(): array {

        $shortopts = 'c:h:';

        $longopts = [
            "command:",
            "name:",
            "text:",
            "receiver:",
            "cron:",
            "help:",
        ];

        return getopt($shortopts, $longopts);
    }

    public function isNeedHelp(array $options): bool {

        return !isset($options['name']) ||
            !isset($options['text']) ||
            !isset($options['receiver']) ||
            !isset($options['cron']) ||
            isset($options['help']) ||
            isset($options['h']);
    }

    private function showHelp() {

        echo " Это тестовый скрипт добавления правил

	Чтобы добавить правило нужно перечислить следующие поля:

	--name Имя события
	--text Текст, который будет отправлен по событию
	--cron Расписания отправки в формате cron
	--receiver Идентификатор получателя сообщения

	Для справки используйте флаги -h или --help

";
    }

}
