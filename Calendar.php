<?php

namespace Frogg;

use Frogg\Calendar\Google;
use Frogg\Calendar\Microsoft;

class Calendar{
	/**
	 *
	 * @var SocialAuth\Networks\Base
	 */
	protected $provider;

	/**
	 * Example:
	 * 		$calendar = new Calendar('google', [
	 *			'callback'	=> 'http://example.com/calendar/google',
	 *			'key'		=> $google_key,
	 *			'secret'	=> $google_secret,
	 *		]);
	 *
	 * @param string $calendarProvider - ['google' || 'microsoft']
	 * @param array $configs - ['key' => '', 'secret' => '', 'callback' => '']
	 * @throws \Exception
	 */
	public function __construct(string $calendarProvider, array $configs){
		if(!isset($configs['key']) || !isset($configs['secret']) || !isset($configs['callback'])){
			throw new \Exception('The configuration array does not contain the element(s) "key" and/or "secret"  and/or "callback"');
		}

		switch($calendarProvider){
			case 'google':
				$this->provider = new Google($configs);
				break;
			case 'microsoft':
				$this->provider = new Microsoft($configs);
				break;
			default:
				throw new \Exception(sprintf('Calendar provider "%s" is not supported',$calendarProvider));
		}
	}

	/**
	 * Example:
	 * 		$authUrl = $calendar->getAuthUrl()
	 * 		$this->redirect($authUrl);
	 */
	public function getAuthUrl(){
		return $this->provider->getAuthUrl();
	}

	/**
	 * Example:
	 * 		//Call this on the callbackURL
	 * 		$calendar->login();
	 */
	public function login(){
		return $this->provider->login();
	}

	/**
	 * Example:
	 * //Call this instead of $login when you already have the access token
	 *
	 * $accessToken = [
	 *		'access_token'	=> 'ACCESS_TOKEN',
	 *		'expires'		=> EXPIRATION_TIMESTAMP,
	 *		'refresh_token'	=> 'REFRESH_TOKEN',
	 *	];
	 *
	 *	$calendar->setAccessToken($accessToken);
	 */
	public function setAccessToken($accessToken){
		return $this->provider->setAccessToken($accessToken);
	}

	public function getBusySlots($params){
		return $this->provider->getBusySlots($params);
	}
	/**
	 * Example:
	 * 		$eventId = $calendar->addEvent([
	 *			'summary'		=> 'event summary etc',
	 *			'description'	=> 'etc event description',
	 *			'start'			=> $selectedSlot['start'],
	 *			'end'			=> $selectedSlot['end'],
	 *		]);
	 *
	 */
	public function addEvent($params){
		return $this->provider->addEvent($params);
	}

	/**
	 * Example:
	 * 		$eventId = 'EVENT_ID';
	 *		$calendar->updateEvent([
	 *			'eventId'		=> $eventId,
	 *			'summary'		=> 'event summary update',
	 *			'description'	=> 'event description',
	 *			'start'			=> $selectedSlot['start'],
	 *			'end'			=> $selectedSlot['end'],
	 *		]);
	 *
	 */
	public function updateEvent($params){
		return $this->provider->updateEvent($params);
	}

	/**
	 * Example:
	 * 		$freeSlots = $calendar->getFreeSlots([
	 *			'from'		=> time(),
	 *			'to'		=> time() + Time::DAY*6,
	 *			'duration'	=> 120,
	 *			'fromHour'	=> 15,
	 *			'toHour'	=> 21,
	 *			'timezone'	=> 'America/Sao_Paulo',
	 *		]);
	 *
	 * @param array $config An array containing the configuration to create the timeslots. The configs available are:
	 * 	- 'from' [default=time()] The unixtstamp of the first second available for the timeslots
	 * 	- 'to' [required] The unixtstamp of the last second available for the timeslots
	 *  - 'duration' [required] the duration of a timeslot in minutes
	 *  - 'timezone' [default='Etc/UTC'] the timezone of the data sent in this configuration array. All the data(from,to,fromHour,toHour) must use the same tstamp.
	 *  - 'fromHour' [default=0] defines the first hour of the day available for the timeslots
	 *  - 'toHour' [default=24] defines the last hour of the day available for the timeslots
	 *  - 'weekDays' [default=array(1,2,3,4,5,6,7)] defines which days of the week are available for the timeslots
	 * @throws \Exception
	 * @return array containing the free time slots as defined in the config param. Each time slot is an array with the elements "start" ans "end". The values returned are always on the 'Etc/UTC timezone'
	 */
	public function getFreeSlots($config){
		$slots = $this->getSlots($config);
		$busySlots = $this->getBusySlots([
			'from' => $config['from'],
			'to' => $config['to']
		]);

		$freeSlots = [];
		foreach ($slots as $slot) {
			$isFree = true;
			foreach ($busySlots as $busySlot) {
				if($this->inRange($slot, $busySlot) || $this->inRange($busySlot, $slot)){
					$isFree = false;
					break;
				}
			}
			if($isFree){
				$freeSlots[] = $slot;
			}
		}

		return $freeSlots;
	}

	private function inRange($needle, $stack){
		return ($needle['start'] >= $stack['start'] && $needle['start'] < $stack['end']) || ($needle['end'] > $stack['start'] && $needle['end'] <= $stack['end']);
	}

	/**
	 * Example:
	 * 		$timeSlots = $calendar->getSlots([
	 *			'from'		=> time(),
	 *			'to'		=> time() + Time::DAY*6,
	 *			'duration'	=> 120,
	 *			'fromHour'	=> 15,
	 *			'toHour'	=> 21,
	 *			'timezone'	=> 'America/Sao_Paulo',
	 *		]);
	 *
	 * @param array $config An array containing the configuration to create the timeslots. The configs available are:
	 * 	- 'from' [default=time()] The unixtstamp of the first second available for the timeslots
	 * 	- 'to' [required] The unixtstamp of the last second available for the timeslots
	 *  - 'duration' [required] the duration of a timeslot in minutes
	 *  - 'timezone' [default='Etc/UTC'] the timezone of the data sent in this configuration array. All the data(from,to,fromHour,toHour) must use the same tstamp.
	 *  - 'fromHour' [default=0] defines the first hour of the day available for the timeslots
	 *  - 'toHour' [default=24] defines the last hour of the day available for the timeslots
	 *  - 'weekDays' [default=array(1,2,3,4,5,6,7)] defines which days of the week are available for the timeslots
	 * @throws \Exception
	 * @return array containing the time slots defined in the config param. Each time slot is an array with the elements "start" ans "end". The values returned are always on the 'Etc/UTC timezone'
	 */
	public function getSlots($config){
		if(!isset($config['to']) || !isset($config['duration'])){
			throw new \Exception('The configuration array does not contain the element(s) "to"(unixtstamp) and/or "duration"(minutes)');
		}

		if(!isset($config['from'])) $config['from'] = time();
		if(!isset($config['timezone'])) $config['timezone'] = 'Etc/UTC';

		//fromHour and toHour are used to define the hour range within each day for which the slots will be created
		if(!isset($config['fromHour'])) $config['fromHour'] = 0;
		if(!isset($config['toHour'])) $config['toHour'] = 24;

		/* weekDays define the days of the week for which the slots will be created
		 * [
		 * 	1 - monday
		 * 	2 - tuesday
		 * 	3 - wednesday
		 * 	4 - thursday
		 * 	5 - friday
		 * 	6 - saturday
		 * 	7 - sunday
		 * ]
		 */
		if(!isset($config['weekDays'])) $config['weekDays'] = [1,2,3,4,5];

		$slots = [];

		$currentDate	= explode('-', gmdate('j-n-Y', $config['from']));
		$firstTime		= gmmktime(0, 0, 0, $currentDate[1], $currentDate[0], $currentDate[2]); //First second of today
		$startingTimes	= [];
		$offset = - timezone_offset_get( timezone_open( $config['timezone'] ), new \DateTime() );

		$todayStartingTime = $firstTime;
		for ($i=0; $todayStartingTime < $config['to']; $i++) {
			$todayStartingTime = $firstTime + ($i*Time::DAY);
			if( in_array( gmdate('N', $todayStartingTime + $offset), $config['weekDays']) ){
				$startingTimes[] = $todayStartingTime;
			}
		}

		$currentSecond = time();
		foreach ($startingTimes as $firstSecond) {
			$dayStart	= $firstSecond + ($config['fromHour']*Time::HOUR) + $offset;
			$dayEnd		= $firstSecond + ($config['toHour']*Time::HOUR) + $offset;

			$slotStart = $dayStart;
			while($slotStart <= ($dayEnd-$config['duration']*Time::MINUTE)){
				$slotEnd	= $slotStart + ($config['duration']*Time::MINUTE);
				//First interview slot should be after $config['from']
				if($slotStart >= $config['from'] && $slotEnd <= $config['to']){
					$slots[] = [
						'start' => $slotStart,
						'end' => $slotEnd
					];
				}
				$slotStart	= $slotEnd;
			}
		}

		return $slots;
	}

}