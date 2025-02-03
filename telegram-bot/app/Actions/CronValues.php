<?php

namespace App\Actions;

class CronValues {

    public function __construct() {}

    public function getCronValues(string $cronString): array {

        $cronValues = explode(" ", $cronString);

        $cronValues = array_map(function ($item)
        {
            return $item === "*" ? null : (int)$item;
        },
        $cronValues);

        return $cronValues;
    }

    public function checkCronValues(array $cronValues): bool {

        if (count($cronValues) != 5) {
            return false;
        }

        // Проверка на валидность минут, часов, дней недели, месяцев и дней года и выходных дней в неделе.
        if (isset($cronValues[0])) {
            if (!($cronValues[0] >= 0 && $cronValues[0] <= 59)) { // минуты (0 - 59)
                return false;
            }
        }

        if (isset($cronValues[1])) {
            if (!($cronValues[1] >= 0 && $cronValues[1] <= 23)) { // часы (0 - 23)
                return false; 
            }
        }

        if (isset($cronValues[2])) {
            if (!($cronValues[2] >= 1 && $cronValues[2] <= 31)) { // дни месяца (1 - 31)
                return false;
            }
            if ($cronValues[3] == 2 && $cronValues[2] > 28) { 
                return false;
            }
            if (($cronValues[3] == 4 || $cronValues[3] == 6 || $cronValues[3] == 9 || $cronValues[3] == 11) && $cronValues[2] > 30) { 
                return false;
            }
        }

        if (isset($cronValues[3])) {
            if (!($cronValues[1] >= 0 && $cronValues[3] <= 12)) { // сами месяцы (1 - 12)
                return false; 
            }
        }

        if (isset($cronValues[4])) {
            if (!($cronValues[4] >= 0 && $cronValues[4] <= 6)) { // дни недели (0 - 6)
                return false; 
            }
        } 

        return true;
    }

}

// $cron = new CronValues();

// $cronValues = $cron->getCronValues("59 23 28 2 6");

// if ($cron->checkCronValues($cronValues)) {
//     echo "Cron-строка корректна";
// } else {
//     echo "Cron-строка некорректна";

// }