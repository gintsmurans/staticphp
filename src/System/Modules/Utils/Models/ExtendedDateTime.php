<?php

namespace System\Modules\Utils\Models;

/**
 * Ads some helper clases to the PHP's \DateTime class
 */
class ExtendedDateTime extends \DateTime
{
    public function __construct(string $datetime = '')
    {
        $locale = date_default_timezone_get();
        if (empty($datetime)) {
            parent::__construct();
            $this->setTimezone(new \DateTimeZone($locale));
        } elseif (strlen($datetime) > 0 && $datetime[0] === '@') {
            parent::__construct($datetime);
            $this->setTimezone(new \DateTimeZone($locale));
        } else {
            parent::__construct('', new \DateTimeZone($locale));

            $date_format = 'Y-m-d';
            if (strpos($datetime, ':') !== false) {
                $date_format .= ' H:i';
            }

            $with_format = ExtendedDateTime::createFromFormat($date_format, $datetime, new \DateTimeZone($locale));
            if ($with_format === false) {
                throw new \Exception("Wrong date format: {$datetime}, required: {$date_format}");
            }

            $this->setTimestamp($with_format->getTimestamp());
        }
    }

    public function previousMonth()
    {
        $this->modify('last day of -1 month');
    }

    public function nextMonth()
    {
        $this->modify('first day of +1 month');
    }

    public function startOfTheMonth()
    {
        $this->modify('first day of this month 00:00:00');
    }

    public function endOfTheMonth()
    {
        $this->modify('last day of this month 23:59:59');
    }

    public function startOfTheWeek()
    {
        $this->modify('this week 00:00:00');
    }

    public function endOfTheWeek()
    {
        $this->modify('sunday this week 23:59:59');
    }

    public function startOfTheDay()
    {
        $this->modify('00:00:00');
    }

    public function endOfTheDay()
    {
        $this->modify('23:59:59');
    }

    public static function startOfTheMonthFromTimestamp(int $unixTime)
    {
        $tmp = new ExtendedDateTime("@{$unixTime}");
        $tmp->startOfTheMonth();

        return $tmp->getTimestamp();
    }

    public static function endOfTheMonthFromTimestamp(int $unixTime)
    {
        $tmp = new ExtendedDateTime("@{$unixTime}");
        $tmp->endOfTheMonth();

        return $tmp->getTimestamp();
    }
}
