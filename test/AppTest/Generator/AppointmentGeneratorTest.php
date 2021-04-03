<?php

declare(strict_types=1);

namespace AppTest\Generator;

use App\Generator\AppointmentGenerator;
use App\Generator\AppointmentGeneratorOptions;
use App\Generator\AppointmentGeneratorOptionsInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

use function array_count_values;
use function array_map;
use function in_array;

class AppointmentGeneratorTest extends TestCase
{
    public function testNormalLunchTime()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
        $appGenOptions->setNormalLunchTime(true);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
    }

    public function testAbnormalLunchTime()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
        $appGenOptions->setNormalLunchTime(false);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertFalse($this->checkLunchTime($appGenOptions, $appoints));
    }

    public function testOneStationAndOnePerTenMinutes()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
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
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
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

    public function testDifferentStationAndOnePerTenMinutesFullCheck()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 1,
            10 => 2,
            20 => 1,
            30 => 2,
            40 => 1,
            50 => 2,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 08:10'), $appoints[1]);
        $this->assertEquals(new DateTime('2021-02-08 08:10'), $appoints[2]);
        $this->assertEquals(new DateTime('2021-02-08 08:20'), $appoints[3]);
        $this->assertEquals(new DateTime('2021-02-08 08:30'), $appoints[4]);
        $this->assertEquals(new DateTime('2021-02-08 08:30'), $appoints[5]);
        $this->assertEquals(new DateTime('2021-02-08 08:40'), $appoints[6]);
        $this->assertEquals(new DateTime('2021-02-08 08:50'), $appoints[7]);
        $this->assertEquals(new DateTime('2021-02-08 08:50'), $appoints[8]);
        $this->assertEquals(new DateTime('2021-02-08 09:00'), $appoints[9]);
        $this->assertEquals(new DateTime('2021-02-08 09:10'), $appoints[10]);
        $this->assertEquals(new DateTime('2021-02-08 09:10'), $appoints[11]);
        $this->assertEquals(new DateTime('2021-02-08 09:20'), $appoints[12]);
        $this->assertEquals(new DateTime('2021-02-08 09:30'), $appoints[13]);
        $this->assertEquals(new DateTime('2021-02-08 09:30'), $appoints[14]);
        $this->assertEquals(new DateTime('2021-02-08 09:40'), $appoints[15]);
        $this->assertEquals(new DateTime('2021-02-08 09:50'), $appoints[16]);
        $this->assertEquals(new DateTime('2021-02-08 09:50'), $appoints[17]);
        $this->assertEquals(new DateTime('2021-02-08 10:00'), $appoints[18]);
        $this->assertEquals(new DateTime('2021-02-08 10:10'), $appoints[19]);
        $this->assertEquals(new DateTime('2021-02-08 10:10'), $appoints[20]);
        $this->assertEquals(new DateTime('2021-02-08 10:20'), $appoints[21]);
        $this->assertEquals(new DateTime('2021-02-08 10:30'), $appoints[22]);
        $this->assertEquals(new DateTime('2021-02-08 10:30'), $appoints[23]);
        $this->assertEquals(new DateTime('2021-02-08 10:40'), $appoints[24]);
        $this->assertEquals(new DateTime('2021-02-08 10:50'), $appoints[25]);
        $this->assertEquals(new DateTime('2021-02-08 10:50'), $appoints[26]);
        $this->assertEquals(new DateTime('2021-02-08 11:00'), $appoints[27]);
        $this->assertEquals(new DateTime('2021-02-08 11:10'), $appoints[28]);
        $this->assertEquals(new DateTime('2021-02-08 11:10'), $appoints[29]);
        $this->assertEquals(new DateTime('2021-02-08 11:20'), $appoints[30]);
        $this->assertEquals(new DateTime('2021-02-08 11:30'), $appoints[31]);
        $this->assertEquals(new DateTime('2021-02-08 11:30'), $appoints[32]);
        $this->assertEquals(new DateTime('2021-02-08 11:40'), $appoints[33]);
        $this->assertEquals(new DateTime('2021-02-08 11:50'), $appoints[34]);
        $this->assertEquals(new DateTime('2021-02-08 11:50'), $appoints[35]);
        $this->assertEquals(new DateTime('2021-02-08 13:00'), $appoints[36]);
        $this->assertEquals(new DateTime('2021-02-08 13:10'), $appoints[37]);
        $this->assertEquals(new DateTime('2021-02-08 13:10'), $appoints[38]);
        $this->assertEquals(new DateTime('2021-02-08 13:20'), $appoints[39]);
        $this->assertEquals(new DateTime('2021-02-08 13:30'), $appoints[40]);
        $this->assertEquals(new DateTime('2021-02-08 13:30'), $appoints[41]);
        $this->assertEquals(new DateTime('2021-02-08 13:40'), $appoints[42]);
        $this->assertEquals(new DateTime('2021-02-08 13:50'), $appoints[43]);
        $this->assertEquals(new DateTime('2021-02-08 13:50'), $appoints[44]);
        $this->assertEquals(new DateTime('2021-02-08 14:00'), $appoints[45]);
        $this->assertEquals(new DateTime('2021-02-08 14:10'), $appoints[46]);
        $this->assertEquals(new DateTime('2021-02-08 14:10'), $appoints[47]);
        $this->assertEquals(new DateTime('2021-02-08 14:20'), $appoints[48]);
        $this->assertEquals(new DateTime('2021-02-08 14:30'), $appoints[49]);
        $this->assertEquals(new DateTime('2021-02-08 14:30'), $appoints[50]);
        $this->assertEquals(new DateTime('2021-02-08 14:40'), $appoints[51]);
        $this->assertEquals(new DateTime('2021-02-08 14:50'), $appoints[52]);
        $this->assertEquals(new DateTime('2021-02-08 14:50'), $appoints[53]);
        $this->assertEquals(new DateTime('2021-02-08 15:00'), $appoints[54]);
        $this->assertEquals(new DateTime('2021-02-08 15:10'), $appoints[55]);
        $this->assertEquals(new DateTime('2021-02-08 15:10'), $appoints[56]);
        $this->assertEquals(new DateTime('2021-02-08 15:20'), $appoints[57]);
        $this->assertEquals(new DateTime('2021-02-08 15:30'), $appoints[58]);
        $this->assertEquals(new DateTime('2021-02-08 15:30'), $appoints[59]);
        $this->assertEquals(new DateTime('2021-02-08 15:40'), $appoints[60]);
        $this->assertEquals(new DateTime('2021-02-08 15:50'), $appoints[61]);
        $this->assertEquals(new DateTime('2021-02-08 15:50'), $appoints[62]);
        $this->assertEquals(new DateTime('2021-02-08 16:00'), $appoints[63]);
        $this->assertEquals(new DateTime('2021-02-08 16:10'), $appoints[64]);
        $this->assertEquals(new DateTime('2021-02-08 16:10'), $appoints[65]);
        $this->assertEquals(new DateTime('2021-02-08 16:20'), $appoints[66]);
        $this->assertEquals(new DateTime('2021-02-08 16:30'), $appoints[67]);
        $this->assertEquals(new DateTime('2021-02-08 16:30'), $appoints[68]);
        $this->assertEquals(new DateTime('2021-02-08 16:40'), $appoints[69]);
        $this->assertEquals(new DateTime('2021-02-08 16:50'), $appoints[70]);
        $this->assertEquals(new DateTime('2021-02-08 16:50'), $appoints[71]);
        $this->assertEquals(new DateTime('2021-02-08 17:00'), $appoints[72]);
        $this->assertEquals(new DateTime('2021-02-08 17:10'), $appoints[73]);
        $this->assertEquals(new DateTime('2021-02-08 17:10'), $appoints[74]);
        $this->assertEquals(new DateTime('2021-02-08 17:20'), $appoints[75]);
        $this->assertEquals(new DateTime('2021-02-08 17:30'), $appoints[76]);
        $this->assertEquals(new DateTime('2021-02-08 17:30'), $appoints[77]);
        $this->assertEquals(new DateTime('2021-02-08 17:40'), $appoints[78]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[79]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[80]);
    }

    public function testDifferentStationAndOnePerTenMinutes()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
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
    }

    public function testFourStationAndOnePerTenMinutesWithMultiDay()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-12 18:00"));
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
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 12:00"));
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
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 12:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
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

    public function testStationAndNotAllTheTime()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-23 14:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-27 15:40"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 0,
            10 => 0,
            20 => 0,
            30 => 1,
            40 => 0,
            50 => 0,
        ]);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $appointmentCountsString = array_map(function($date) { return $date->format('Y-m-d H:i:s'); }, $appoints);
        $appointmentCounts = array_count_values($appointmentCountsString);
        $appointCount = 10;

        $this->assertEquals($appointmentCounts['2021-02-23 14:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-24 14:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-25 14:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-26 14:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-27 14:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-23 15:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-24 15:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-25 15:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-26 15:30:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-27 15:30:00'], 1);

        $this->assertCount($appointCount, $appoints);
    }

    public function testStationAndNotAllTheTimeAndDiffIntervals()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-23 14:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-27 15:40"));
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

        $appointmentCountsString = array_map(function($date) { return $date->format('Y-m-d H:i:s'); }, $appoints);
        $appointmentCounts = array_count_values($appointmentCountsString);
        $appointCount = 155;

        $this->assertEquals($appointmentCounts['2021-02-23 14:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-24 14:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-25 14:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-26 14:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-27 14:00:00'], 1);

        $this->assertEquals($appointmentCounts['2021-02-23 14:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-24 14:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-25 14:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-26 14:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-27 14:10:00'], 2);

        $this->assertEquals($appointmentCounts['2021-02-23 14:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-24 14:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-25 14:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-26 14:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-27 14:20:00'], 3);

        $this->assertEquals($appointmentCounts['2021-02-23 14:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-24 14:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-25 14:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-26 14:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-27 14:30:00'], 4);

        $this->assertEquals($appointmentCounts['2021-02-23 14:40:00'], 5);
        $this->assertEquals($appointmentCounts['2021-02-24 14:40:00'], 5);
        $this->assertEquals($appointmentCounts['2021-02-25 14:40:00'], 5);
        $this->assertEquals($appointmentCounts['2021-02-26 14:40:00'], 5);
        $this->assertEquals($appointmentCounts['2021-02-27 14:40:00'], 5);

        $this->assertEquals($appointmentCounts['2021-02-23 14:50:00'], 6);
        $this->assertEquals($appointmentCounts['2021-02-24 14:50:00'], 6);
        $this->assertEquals($appointmentCounts['2021-02-25 14:50:00'], 6);
        $this->assertEquals($appointmentCounts['2021-02-26 14:50:00'], 6);
        $this->assertEquals($appointmentCounts['2021-02-27 14:50:00'], 6);

        $this->assertEquals($appointmentCounts['2021-02-23 15:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-24 15:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-25 15:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-26 15:00:00'], 1);
        $this->assertEquals($appointmentCounts['2021-02-27 15:00:00'], 1);

        $this->assertEquals($appointmentCounts['2021-02-23 15:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-24 15:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-25 15:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-26 15:10:00'], 2);
        $this->assertEquals($appointmentCounts['2021-02-27 15:10:00'], 2);

        $this->assertEquals($appointmentCounts['2021-02-23 15:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-24 15:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-25 15:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-26 15:20:00'], 3);
        $this->assertEquals($appointmentCounts['2021-02-27 15:20:00'], 3);

        $this->assertEquals($appointmentCounts['2021-02-23 15:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-24 15:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-25 15:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-26 15:30:00'], 4);
        $this->assertEquals($appointmentCounts['2021-02-27 15:30:00'], 4);

        $this->assertCount($appointCount, $appoints);
    }

    public function testNotFullStartHour()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:20"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
        $appGenOptions->setNormalLunchTime(true);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertCount(52, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:20'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:50'), $appoints[51]);
    }

    public function testNotFullEndHour()
    {
        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 17:40"));
        $appGenOptions->setNormalLunchTime(true);

        $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

        $this->assertCount(52, $appoints);
        $this->assertEquals(new DateTime('2021-02-08 08:00'), $appoints[0]);
        $this->assertEquals(new DateTime('2021-02-08 17:30'), $appoints[51]);
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
