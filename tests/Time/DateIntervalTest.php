<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 06/12/17
 * Time: 15:13
 */

use Frogg\Time\DateInterval;
use Frogg\Time;
use PHPUnit\Framework\TestCase;

class DateIntervalTest extends TestCase
{

    public function testToYears()
    {
        $now      = time();
        $interval = new DateInterval($now - (365 * Time::DAY), $now);
        $this->assertEquals(1, $interval->toYears());
    }

    public function testToMonths()
    {
        $now      = time();
        $interval = new DateInterval($now - (60 * Time::DAY), $now);
        $this->assertEquals(2, $interval->toMonths());
    }

    public function testToDays()
    {
        $now      = time();
        $interval = new DateInterval($now - (5 * Time::DAY), $now);
        $this->assertEquals(5, $interval->toDays());
    }

    public function testToHours()
    {
        $now      = time();
        $interval = new DateInterval($now - (10 * Time::HOUR), $now);
        $this->assertEquals(10, $interval->toHours());
    }

    public function testToMinutes()
    {
        $now      = time();
        $interval = new DateInterval($now - (20 * Time::MINUTE), $now);
        $this->assertEquals(20, $interval->toMinutes());
    }

    public function testToSeconds()
    {
        $now      = time();
        $interval = new DateInterval($now - (10 * Time::SECOND), $now);
        $this->assertEquals(10, $interval->toSeconds());
    }
}
