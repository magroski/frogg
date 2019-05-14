<?php

namespace Frogg;

use Frogg\Time\DateInterval;

/**
 * @deprecated Use magroski/time-buddy
 */
class Time
{

    const SECOND = 1;       //Seconds per Second
    const MINUTE = 60;      //Seconds per minute
    const HOUR   = 3600;    //Seconds per hour
    const DAY    = 86400;   //Seconds per day

    private $time;

    /**
     * Constructor
     *
     * @param int|null $time Unix timestamp OR a string timestamp (YYYY-MM-DD)
     * @throws \Exception
     */
    public function __construct($time = 0)
    {
        if (is_numeric($time)) {
            $this->time = ($time) ? $time : time();
        } else {
            $date       = new \DateTime($time);
            $this->time = mktime($date->format('H'), $date->format('i'), $date->format('s'), $date->format('n'), $date->format('d'), $date->format('Y'));
        }
    }

    /**
     * Returns the date formatted as an Unix timestamp
     */
    public function __toString() : string
    {
        return $this->getUnixTstamp() . '';
    }

    /**
     * Returns the date formatted as an Unix timestamp
     */
    public function getUnixTstamp() : int
    {
        return $this->time;
    }

    /**
     * Returns the date formatted as a database timestamp (yyyy-mm-dd hh:ii:ss)
     */
    public function getTstamp() : string
    {
        return $this->getYear() . '-' . $this->getMonth() . '-' . $this->getDay() . ' ' . $this->getHours() . ':' . $this->getMinutes() . ':' . $this->getSeconds();
    }

    /**
     * Returns the year of the stored time variable
     */
    public function getYear() : string
    {
        return date('Y', $this->time);
    }

    /**
     * Returns the month of the stored time variable  <br/>
     * Value between 01 and 12 (with leading zeros)
     */
    public function getMonth() : string
    {
        return date('m', $this->time);
    }

    /**
     * Returns the day of the month of the stored time variable  <br/>
     * Value between 01 and 31 (with leading zeros)
     */
    public function getDay() : string
    {
        return date('d', $this->time);
    }

    /**
     * Returns the day of the month of the stored time variable  <br/>
     * Value between 1 and 31 (without leading zeros)
     */
    public function getDayNoZero() : string
    {
        return date('j', $this->time);
    }

    /**
     * Returns the hour of the day of the stored time variable  <br/>
     * Value between 00 and 23 (with leading zeros)
     */
    public function getHours() : string
    {
        return date('H', $this->time);
    }

    /**
     * Returns the minute of the hour of the stored time variable  <br/>
     * Value between 00 and 59 (with leading zeros)
     *
     * @return string Date minute as string
     */
    public function getMinutes() : string
    {
        return date('i', $this->time);
    }

    /**
     * Returns the seconds of the minute of the stored time variable  <br/>
     * Value between 00 and 59 (with leading zeros)
     */
    public function getSeconds() : string
    {
        return date('s', $this->time);
    }

    /**
     * Adds time to the stored time variable
     *
     * @param $seconds int Desired quantity of seconds to add to the current time
     *
     * @return Time
     *
     */
    public function add(int $seconds) : self
    {
        $this->time += $seconds;

        return $this;
    }

    /**
     * Subtracts time from the stored time variable
     *
     * @param int $seconds Desired quantity of seconds to subtract from the current time
     *
     * @return Time
     */
    public function subtract(int $seconds) : self
    {
        $this->time -= $seconds;

        return $this;
    }

    /**
     * Calculates the absolute difference between the stored time and the $time parameter  <br/>
     * Returns a {@link DateInterval} object representing the time difference
     *
     * @param mixed $time Unix timestamp OR a string timestamp (YYYY-MM-DD)
     *
     * @return DateInterval
     */
    public function diff($time) : DateInterval
    {
        $tmp = new self($time);

        return new DateInterval(abs($this->time - $tmp->getUnixTstamp()));
    }

    /**
     * This method transforms the stored time variable in a string according to the informed mask <br/>
     * Ex: Mask -> 'Y/m/d % H' results in '2010/02/15 % 02'
     *
     * @param string $mask String mask that will be used to format the time. <p>
     *                     Y-> 2010  <br/>
     *                     y-> 10  <br/>
     *                     m-> 02  <br/>
     *                     M-> Feb  <br/>
     *                     F-> February  <br/>
     *                     d-> 15  <br/>
     *                     D-> Mon  <br/>
     *                     l-> Monday  <br/>
     *                     H-> 02  <br/>
     *                     i-> 43  <br/>
     *                     s-> 38  <br/>
     *                     More mask values in -> http://www.php.net/manual/en/function.date.php </p>
     *
     * @return string
     */
    public function format(string $mask) : string
    {
        /**
         *
         *  IMPLEMENTAR ESSE METODO NOVAMENTE PARA SUPORTE DAS TRADUCOES COMENTADAS
         *
         */
        // require 'Frogg/lang/'.LANGUAGE.'.php';

        // /* D-># */ $sem 	= array($lang['Sun'],$lang['Mon'],$lang['Tue'],$lang['Wed'],$lang['Thu'],$lang['Fri'],$lang['Sat']);
        // /* l->$ */ $semana 	= array($lang['Sunday'],$lang['Monday'],$lang['Tuesday'],$lang['Wednesday'],$lang['Thursday'],$lang['Friday'],$lang['Saturday']);
        // /* M->% */ $mes 	= array('',$lang['Jan'],$lang['Feb'],$lang['Mar'],$lang['Apr'],$lang['May'],$lang['Jun'],$lang['Jul'],$lang['Aug'],$lang['Sep'],$lang['Oct'],$lang['Nov'],$lang['Dec']);
        // /* F->& */ $meses	= array('',$lang['January'],$lang['February'],$lang['March'],$lang['April'],$lang['May'],$lang['June'],$lang['July'],$lang['August'],$lang['September'],$lang['October'],$lang['November'],$lang['December']);

        // $patterns 	  = array('/D/','/l/','/M/','/F/');
        // $replacements = array('#','q','%','}');
        // $mask = preg_replace($patterns, $replacements, $mask);

        $mask = date($mask, $this->time);
        // $patterns 	  = array('/#/','/q/','/%/','/}/');
        // $replacements = array($sem[date('w',$this->time)],$semana[date('w',$this->time)],$mes[date('n',$this->time)],$meses[date('n',$this->time)]);

        // return preg_replace($patterns, $replacements, $mask);
        return $mask;
    }

    /**
     * Transforms minutes in seconds
     *
     * @param int $minutes Quantity of minutes to be transformed into seconds
     *
     * @return int
     */
    public static function secondsFromMinutes(int $minutes) : int
    {
        return self::MINUTE * $minutes;
    }

    /**
     * Transforms hours in seconds
     *
     * @param int $hours Quantity of hours to be transformed into seconds
     *
     * @return int
     */
    public static function secondsFromHours(int $hours) : int
    {
        return self::HOUR * $hours;
    }

    /**
     * Transforms days in seconds
     *
     * @param int $days Quantity of days to be transformed into seconds
     *
     * @return int
     */
    public static function secondsFromDays(int $days) : int
    {
        return self::DAY * $days;
    }
}
