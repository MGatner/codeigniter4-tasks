<?php namespace CodeIgniter\Tasks;

use CodeIgniter\I18n\Time;

class CronExpression
{
	/**
	 * The timezone this should be considered under.
	 *
	 * @var \DateTimeZone
	 */
	protected $timezone;

	/**
	 * The current date/time. Used for testing.
	 *
	 * @var \DateTime
	 */
	protected $testTime;

	/**
	 * Allows us to set global timezone for all tasks
	 * on construct
	 *
	 * @param string $timezone The global timezone for all tasks
	 *
	 * @return void
	 */
	public function __construct(string $timezone = null)
	{
		$this->setTimezone($timezone);
	}

	/**
	 * Checks whether cron expression should be run. Allows
	 * for custom timezone to be used for specific task
	 *
	 * @param string $expression The Cron Expression to be evaluated
	 *
	 * @return boolean
	 */
	public function shouldRun(string $expression): bool
	{
		// Set our current time
		if (! $this->testTime instanceof Time)
		{
			$this->testTime = Time::now($this->timezone);
		}

		// Break the expression into separate parts
		[
			$min,
			$hour,
			$monthDay,
			$month,
			$weekDay,
		] = explode(' ', $expression);

		return $this->checkMinute($min)
			&& $this->checkHour($hour)
			&& $this->checkMonthDay($monthDay)
			&& $this->checkMonth($month)
			&& $this->checkWeekDay($weekDay);
	}

	/**
	 * Returns a Time instance representing the next
	 * date/time this expression would be ran.
	 *
	 * @param string $expression
	 *
	 * @return Time
	 */
	public function nextRun(string $expression): Time
	{
		return new Time();
	}

	/**
	 * Returns a Time instance representing the last
	 * date/time this expression would have ran.
	 *
	 * @param string $expression
	 *
	 * @return Time
	 */
	public function lastRun(string $expression): Time
	{
		return new Time();
	}

	/**
	 * Sets the timezone that dates should be examined under.
	 *
	 * @param string $timezone
	 *
	 * @return $this
	 * @throws \Exception Thrown if $timezone is not a valid timezone string.
	 */
	public function setTimezone(string $timezone = null)
	{
		if ($timezone === null)
		{
			$timezone = app_timezone();
		}

		// Throws exception if invalid Timezone is given
		$this->timezone = new \DateTimeZone($timezone);

		// If testTime has been set, convert it to the new timezone
		if ($this->testTime instanceof Time)
		{
			$this->testTime = $this->testTime->setTimezone($this->timezone);
		}
		return $this;
	}

	/**
	 * Sets a date/time that will be used in place
	 * of the current time to help with testing.
	 *
	 * @param string $dateTime
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function testTime(string $dateTime)
	{
		$this->testTime = Time::parse($dateTime, $this->timezone);

		return $this;
	}

	protected function checkMinute(string $time): bool
	{
		return $this->checkTime($time, 'i');
	}

	protected function checkHour(string $time): bool
	{
		return $this->checkTime($time, 'G');
	}

	protected function checkMonthDay(string $time): bool
	{
		return $this->checkTime($time, 'j');
	}

	protected function checkMonth(string $time): bool
	{
		return $this->checkTime($time, 'n');
	}

	protected function checkWeekDay(string $time): bool
	{
		return $this->checkTime($time, 'w');
	}

	/**
	 * @param string $time
	 * @param string $format
	 *
	 * @return boolean
	 */
	protected function checkTime(string $time, string $format): bool
	{
		if ($time === '*')
		{
			return true;
		}

		$currentTime = $this->testTime->format($format);

		// Handle repeating times (i.e. /5 for every 5 minutes)
		if (strpos($time, '/') === 0)
		{
			$period = substr($time, 1);
			return ($currentTime % $period) === 0;
		}
		// Handle ranges (1-5)
		elseif (strpos($time, '-') !== false)
		{
			$items = [];
			[
				$start,
				$end,
			]      = explode('-', $time);

			for ($i = $start; $i <= $end; $i++)
			{
				$items[] = $i;
			}
		}
		// Handle multiple days
		else
		{
			$items = explode(',', $time);
		}

		return in_array($currentTime, $items);
	}
}
