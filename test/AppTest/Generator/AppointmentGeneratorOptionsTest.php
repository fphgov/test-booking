<?php

declare(strict_types=1);

namespace AppTest\Service;

use App\Generator\AppointmentGeneratorOptions;
use DateTime;
use PHPUnit\Framework\TestCase;

class AppointmentGeneratorOptionsTest extends TestCase
{
    public function testArrayParser()
    {
        $options = [
            'startTime' => 8,
            'endTime'   => 18,
            'startDate' => '2021-02-08',
            'endDate'   => '2021-02-12',
            'interval'  => [
                0  => 1,
                10 => 1,
                20 => 1,
                30 => 1,
                40 => 1,
                50 => 1,
            ],
        ];

        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->parseFromArray($options);

        $this->assertEquals($appGenOptions->getStartTime(), $options['startTime']);
        $this->assertEquals($appGenOptions->getEndTime(), $options['endTime']);
        $this->assertEquals($appGenOptions->getStartDate(), new DateTime('2021-02-08 08:00:00.00'));
        $this->assertEquals($appGenOptions->getEndDate(), new DateTime('2021-02-12 18:00:00.00'));
        $this->assertEquals($appGenOptions->getIntervalMatrix(), $options['interval']);
    }
}
