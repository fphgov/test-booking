<?php

declare(strict_types=1);

namespace AppTest\Generator;

use App\Generator\AppointmentGeneratorOptions;
use DateTime;
use PHPUnit\Framework\TestCase;

class AppointmentGeneratorOptionsTest extends TestCase
{
    public function testArrayParser()
    {
        $options = [
            'startDateTime' => '2021-02-08 08:00',
            'endDateTime'   => '2021-02-12 18:00',
            'interval'      => [
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

        $this->assertEquals($appGenOptions->getStartTime(), 8);
        $this->assertEquals($appGenOptions->getEndTime(), 18);
        $this->assertEquals($appGenOptions->getStartDateTime(), new DateTime('2021-02-08 08:00'));
        $this->assertEquals($appGenOptions->getEndDateTime(), new DateTime('2021-02-12 18:00'));
        $this->assertEquals($appGenOptions->getIntervalMatrix(), $options['interval']);
    }
}
