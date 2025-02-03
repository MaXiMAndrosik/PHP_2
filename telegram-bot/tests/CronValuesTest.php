<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers CronValues
 */

class CronValuesTest extends TestCase {

     /**
    * @dataProvider cronValuesDataProvider
    */

    public function testgetCronValuesMapArrayReturnMapedArray(string $cronString, array $cronStringResult) {

        $cronValues = new App\Actions\CronValues();

        $result = $cronValues->getCronValues($cronString);

        self::assertEquals($result, $cronStringResult);

    }

    public static function cronValuesDataProvider() {

        return [
            ['* * * * *' => '* * * * *', [null, null, null, null, null]],
            ['1 * * * *' => '1 * * * *', [1, null, null, null, null]],
            ['1 2 * * *' => '1 2 * * *', [1, 2, null, null, null]],
            ['1 2 3 * *' => '1 2 3 * *', [1, 2, 3, null, null]],
            ['1 2 3 4 *' => '1 2 3 4 *', [1, 2, 3, 4, null]],
            ['1 2 3 4 5' => '1 2 3 4 5', [1, 2, 3, 4, 5]],
            ['1 2 3 4' => '1 2 3 4', [1, 2, 3, 4]],
            ['1 * 3' => '1 2 *', [1, 2, null]],
            ['5' => '5', [5]]
        ];
    }


    /**
    * @dataProvider checkCronValuesDataProvider
    */

    public function testCheckCronValuesTrueOrFalse(array $cronArray, bool $cronArrayCheck) {

        $cronValues = new App\Actions\CronValues();

        $result = $cronValues->checkCronValues($cronArray);

        self::assertEquals($result, $cronArrayCheck);

    }

    public static function checkCronValuesDataProvider() {

        return [
            [[60, 2, 3, 4, 5], false],
            [[1, 24, 3, 4, 5], false],
            [[1, 2, 0, 4, 5], false],
            [[1, 2, 31, 4, 5], false],
            [[1, 2, 31, 2, 5], false],
            [[1, 2, 3, 13, 5], false],
            [[1, 2, 3, 4, 7], false],
            [[1, 2, 3, 4, 5, 6], false],
            [[1, 2, 3, 4, 5], true],
            [[1, 2, 3, 4], false],
            [[1, 2, 3], false],
            [[1, 2], false],
            [[1], false],
            [[], false]
        ];
    }

}