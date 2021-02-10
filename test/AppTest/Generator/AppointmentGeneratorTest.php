<?php

declare(strict_types=1);

namespace AppTest\Generator;

use App\Generator\AppointmentGenerator;
use App\Generator\AppointmentGeneratorOptions;
use App\Generator\AppointmentGeneratorOptionsInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

use function in_array;

class AppointmentGeneratorTest extends TestCase
{
    public function testNormalLunchTime()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
    }

    public function testAbnormalLunchTime()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(false);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
    }

    public function testOneStationAndOnePerTenMinutes()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(54, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[53]);
    }

    public function testFourStationAndOnePerTenMinutes()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 4,
            10 => 4,
            20 => 4,
            30 => 4,
            40 => 4,
            50 => 4,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(216, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[215]);
    }

    public function testDifferentStationAndOnePerTenMinutes()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 1,
            10 => 2,
            20 => 3,
            30 => 4,
            40 => 5,
            50 => 6,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $sum = [];
        foreach ($appoints as $ak => $appoint) {
            if (! isset($sum[$appoint->format('Y-m-d H:i:s')])) {
                $sum[$appoint->format('Y-m-d H:i:s')] = 0;
            }

            $sum[$appoint->format('Y-m-d H:i:s')]++;
        }

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(189, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[188]);
    }

    public function testFourStationAndOnePerTenMinutesWithMultiDay()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-12"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 4,
            10 => 4,
            20 => 4,
            30 => 4,
            40 => 4,
            50 => 4,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(1080, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-12 17:50'), $appoints[1079]);
    }

    public function testFourStationAndOnePerTenMinutesWithMultiDayAM()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(12);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 1,
            10 => 1,
            20 => 1,
            30 => 1,
            40 => 1,
            50 => 1,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(24, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 11:50'), $appoints[23]);
    }

    public function testFourStationAndOnePerTenMinutesWithMultiDayPM()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(12);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 1,
            10 => 1,
            20 => 1,
            30 => 1,
            40 => 1,
            50 => 1,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
        $this->assertCount(30, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 13:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[29]);
    }

    private function checkLunchTime(
        AppointmentGeneratorOptionsInterface $appGenOptions,
        array $appoints
    ): bool {
        $badLunchCalc = false;
        foreach ($appoints as $ak => $appoint) {
            if ($appGenOptions->getNormalLunchTime() && in_array($appoint->format('H:i'), ['12:00', '12:10', '12:20', '12:30', '12:40', '12:50'], true)) {
                $badLunchCalc = true;
            } elseif (! $appGenOptions->getNormalLunchTime() && in_array($appoint->format('H:i'), ['13:00', '13:10', '13:20', '13:30', '13:40', '13:50'], true)) {
                $badLunchCalc = true;
            }
        }

        return $badLunchCalc;
    }
}
