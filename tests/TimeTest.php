<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 31/10/17
 * Time: 16:15
 */

use Frogg\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{

    public function testToString()
    {
        $time = new Time('2010-11-30 12:05:30');
        $this->assertEquals('1291118730', $time->__toString());
    }

    public function testUnixtTimestamp()
    {
        $time = new Time('2010-11-30 12:05:30');
        $this->assertEquals('1291118730', $time->getUnixTstamp());
    }

    public function testGetTstamp()
    {
        $time = new Time(1291118730);
        $this->assertEquals('2010-11-30 12:05:30', $time->getTstamp());
    }

    public function testGetPieces()
    {
        $time = new Time(1494227104);
        $this->assertEquals('2017', $time->getYear());
        $this->assertEquals('05', $time->getMonth());
        $this->assertEquals('08', $time->getDay());
        $this->assertEquals('8', $time->getDayNoZero());
        $this->assertEquals('07', $time->getHours());
        $this->assertEquals('05', $time->getMinutes());
        $this->assertEquals('04', $time->getSeconds());
    }

    public function testArithmetic()
    {
        $time = new Time(1494227104);
        $time->add(Time::DAY);
        $this->assertEquals('09', $time->getDay());
        $time->subtract(Time::HOUR);
        $this->assertEquals('06', $time->getHours());
    }

    public function testDiff()
    {
        $time         = new Time(1494227104);
        $dateInterval = $time->diff(1494227164);
        $this->assertEquals('Frogg\Time\DateInterval', get_class($dateInterval));
    }

    public function testMask()
    {
        $time = new Time(1494227104);
        $this->assertEquals('07:05:04 08/05/2017', $time->format('H:i:s d/m/Y'));
    }

    public function testConversion()
    {
        $this->assertEquals(120, Time::secondsFromMinutes(2));
        $this->assertEquals(7200, Time::secondsFromHours(2));
        $this->assertEquals(86400, Time::secondsFromDays(1));
    }

}