<?php
/**
 * IntlDateTime is an extended version of php 5 DateTime class with integrated
 * IntlDateFormatter functionality which adds support for multiple calendars
 * and locales provided by ICU project. (needs php >= 5.3.0 with php-intl extension)
 * However, this class is not compatible with DateTime class because it uses ICU
 * pattern syntax for formatting and parsing date strings.
 * (@link http://userguide.icu-project.org/formatparse/datetime)
 *
 * @copyright   Copyright 2010, Tartan (https://github.com/iamtartan/)
 * @license     GNU General Public License 3.0 (http://www.gnu.org/licenses/gpl.html)
 * @package     Tartan\Zaman
 * @author      Aboozar Ghaffari <aboozar.ghf@gmail.com>
 */

namespace Tartan\Zaman;

use DateTime;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use NumberFormatter;
use ReturnTypeWillChange;

class IntlDatetime extends DateTime
{

    /**
     * @var string The current locale in use
     */
    protected string $locale;

    /**
     * @var string The current calendar in use
     */
    protected string $calendar;

    /**
     * Creates a new instance of IntlDateTime
     *
     * @param  mixed  $time  Unix timestamp or strtotime() compatible string or another DateTime object
     * @param  mixed  $timezone  DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     * @param  string  $calendar  any calendar supported by ICU (e.g. gregorian, persian, islamic, ...)
     * @param  string  $locale  any locale supported by ICU
     * @param  string|null  $pattern  the date pattern in which $time is formatted.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(
        $time = null,
        $timezone = null,
        string $calendar = 'gregorian',
        string $locale = 'en_US',
        string $pattern = null
    ) {
        if (!isset($timezone)) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        } elseif (!is_a($timezone, 'DateTimeZone')) {
            $timezone = new DateTimeZone($timezone);
        }

        parent::__construct(null, $timezone);
        $this->setLocale($locale);
        $this->setCalendar($calendar);
        if (isset($time)) {
            $this->set($time, null, $pattern);
        }
    }

    /**
     * Alters object's internal timestamp with a string acceptable by strtotime() or a Unix timestamp or a DateTime object.
     *
     * @param  mixed  $time  Unix timestamp or strtotime() compatible string or another DateTime object
     * @param  mixed|null  $timezone  DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     * @param  string|null  $pattern  the date pattern in which $time is formatted.
     *
     * @return IntlDateTime The modified DateTime.
     */
    public function set(mixed $time, mixed $timezone = null, string $pattern = null): static
    {
        if (is_a($time, 'DateTime')) {
            $time = $time->format('U');
        } elseif (!is_numeric($time) || $pattern) {
            if (!$pattern) {
                $pattern = $this->guessPattern($time);
            }

            if (!$pattern && preg_match('/((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i', $time)) {
                if (isset($timezone)) {
                    $tempTimezone = $this->getTimezone();
                    $this->setTimezone($timezone);
                }

                $this->setTimestamp(time());
                $this->modify($time);

                if (isset($timezone)) {
                    $this->setTimezone($tempTimezone);
                }

                return $this;
            }

            $timezone = empty($timezone) ? $this->getTimezone() : $timezone;
            if (is_a($timezone, 'DateTimeZone')) {
                $timezone = $timezone->getName();
            }
            $defaultTimezone = @date_default_timezone_get();
            date_default_timezone_set($timezone);

            if ($pattern) {
                $time = $this->getFormatter(array('timezone' => 'GMT', 'pattern' => $pattern))->parse($time);
                $time -= date('Z', $time);
            } else {
                $time = strtotime($time);
            }

            date_default_timezone_set($defaultTimezone);
        }

        $this->setTimestamp($time);

        return $this;
    }

    /**
     * Returns date formatted according to given pattern.
     *
     * @param  string  $format  Date pattern in ICU syntax (@link http://userguide.icu-project.org/formatparse/datetime)
     * @param  mixed|null  $timezone  DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     *
     * @return string Formatted date on success or FALSE on failure.
     */
    #[ReturnTypeWillChange] public function format(string $format, mixed $timezone = null): string
    {
        if (isset($timezone)) {
            $tempTimezone = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        // Timezones DST data in ICU are not as accurate as PHP.
        // So we get timezone offset from php and pass it to ICU.
        $result = $this->getFormatter(array(
            'timezone' => 'GMT'.(parent::format('Z') ? parent::format('P') : ''),
            'pattern' => $format
        ))->format($this->getTimestamp());

        if (isset($timezone)) {
            $this->setTimezone($tempTimezone);
        }

        return $result;
    }

    /**
     * Sets the timezone for the object.
     *
     * @param  mixed  $timezone  DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     *
     * @return IntlDateTime The modified DateTime.
     */
    #[ReturnTypeWillChange] public function setTimezone($timezone): static
    {
        if (!is_a($timezone, 'DateTimeZone')) {
            $timezone = new DateTimeZone($timezone);
        }
        parent::setTimezone($timezone);

        return $this;
    }

    /**
     * Returns an instance of IntlDateFormatter with specified options.
     *
     * @param  array  $options
     *
     * @return IntlDateFormatter
     */
    protected function getFormatter(array $options = []): IntlDateFormatter
    {
        $locale = empty($options['locale']) ? $this->locale : $options['locale'];
        $calendar = empty($options['calendar']) ? $this->calendar : $options['calendar'];
        $timezone = empty($options['timezone']) ? $this->getTimezone() : $options['timezone'];
        if (is_a($timezone, 'DateTimeZone')) {
            $timezone = $timezone->getName();
        }
        $pattern = empty($options['pattern']) ? null : $options['pattern'];

        return new IntlDateFormatter($locale.'@calendar='.$calendar,
            IntlDateFormatter::FULL, IntlDateFormatter::FULL, $timezone,
            $calendar == 'gregorian' ? IntlDateFormatter::GREGORIAN : IntlDateFormatter::TRADITIONAL, $pattern);
    }

    /**
     * Overrides the getTimestamp method to support timestamps out of the integer range.
     *
     * @return float Unix timestamp representing the date.
     */
    #[ReturnTypeWillChange] public function getTimestamp(): float
    {
        return floatval(parent::format('U'));
    }

    /**
     * Tries to guess the date pattern in which $time is formatted.
     *
     * @param  string  $time  The date string
     *
     * @return bool|string Detected ICU pattern on success, FALSE otherwise.
     */
    protected function guessPattern(string $time): bool|string
    {
        $time = $this->latinizeDigits(trim($time));

        $shortDateRegex = '(\d{2,4})(-|\\\\|/)\d{1,2}\2\d{1,2}';
        $longDateRegex = '([^\d]*\s)?\d{1,2}(-| )[^-\s\d]+\4(\d{2,4})';
        $timeRegex = '\d{1,2}:\d{1,2}(:\d{1,2})?(\s.*)?';

        if (preg_match("@^(?:(?:$shortDateRegex)|(?:$longDateRegex))(\s+$timeRegex)?$@", $time, $match)) {
            if (!empty($match[1])) {
                $separator = $match[2];
                $pattern = strlen($match[1]) == 2 ? 'yy' : 'yyyy';
                $pattern .= $separator.'MM'.$separator.'dd';
            } else {
                $separator = $match[4];
                $pattern = 'dd'.$separator.'LLL'.$separator;
                $pattern .= strlen($match[5]) == 2 ? 'yy' : 'yyyy';
                if (!empty($match[3])) {
                    $pattern = (preg_match('/,\s+$/', $match[3]) ? 'E, ' : 'E ').$pattern;
                }
            }
            if (!empty($match[6])) {
                $pattern .= !empty($match[8]) ? ' hh:mm' : ' HH:mm';
                if (!empty($match[7])) {
                    $pattern .= ':ss';
                }
                if (!empty($match[8])) {
                    $pattern .= ' a';
                }
            }

            return $pattern;
        }

        return false;
    }

    /**
     * Replaces localized digits in $str with latin digits.
     *
     * @param  string  $str
     *
     * @return string Platinized string
     */
    protected function latinizeDigits(string $str): string
    {
        $result = '';
        $num = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);
        preg_match_all('/.[\x80-\xBF]*/', $str, $matches);
        foreach ($matches[0] as $char) {
            $pos = 0;
            $parsedChar = $num->parse($char, NumberFormatter::TYPE_INT32, $pos);
            $result .= $pos ? $parsedChar : $char;
        }

        return $result;
    }

    /**
     * Overrides the setTimestamp method to support timestamps out of the integer range.
     *
     * @param  float  $timestamp  Unix timestamp representing the date.
     *
     * @return IntlDateTime the modified DateTime.
     */
    #[ReturnTypeWillChange] public function setTimestamp($timestamp): static
    {
        $diff = $timestamp - $this->getTimestamp();
        $days = floor($diff / 86400);
        $seconds = $diff - $days * 86400;
        $timezone = $this->getTimezone();
        $this->setTimezone('UTC');
        parent::modify("$days days $seconds seconds");
        $this->setTimezone($timezone);

        return $this;
    }

    /**
     * Alter the timestamp by incrementing or decrementing in a format accepted by strtotime().
     *
     * @param  string  $modifier  a string in a relative format accepted by strtotime().
     *
     * @return IntlDateTime The modified DateTime.
     */
    #[ReturnTypeWillChange] public function modify(string $modifier): static
    {
        $modifier = $this->latinizeDigits(trim($modifier));
        $modifier = preg_replace_callback('/(.*?)((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i',
            array($this, 'modifyCallback'), $modifier);
        if ($modifier) {
            parent::modify($modifier);
        }

        return $this;
    }

    /**
     * Gets the current locale used by the object.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets the locale used by the object.
     *
     * @param  string  $locale
     *
     * @return IntlDateTime The modified DateTime.
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the current calendar used by the object.
     *
     * @return string
     */
    public function getCalendar(): string
    {
        return $this->calendar;
    }

    /**
     * Sets the calendar used by the object.
     *
     * @param  string  $calendar
     *
     * @return IntlDateTime The modified DateTime.
     */
    public function setCalendar(string $calendar): static
    {
        $this->calendar = strtolower($calendar);

        return $this;
    }

    /**
     * Preserve original DateTime::format functionality
     *
     * @param  string  $format  Format accepted by date().
     * @param  mixed|null  $timezone  DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     *
     * @return string Formatted date on success or FALSE on failure.
     */
    public function classicFormat(string $format, mixed $timezone = null): string
    {
        if ($timezone) {
            $tempTimezone = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        $result = parent::format($format);

        if ($timezone) {
            $this->setTimezone($tempTimezone);
        }

        return $result;
    }

    /**
     * echo object as string
     **/
    public function __toString()
    {
        return $this->format('yyyy/MM/dd H:m:s');
    }

    /**
     * Internally used by modify method to calculate calendar-aware modifications
     *
     * @param  array  $matches
     *
     * @return string An empty string
     */
    protected function modifyCallback(array $matches): string
    {
        if (!empty($matches[1])) {
            parent::modify($matches[1]);
        }

        list($y, $m, $d) = explode('-', $this->format('y-M-d'));
        $change = strtolower($matches[2]);
        $unit = strtolower($matches[3]);

        switch ($change) {
            case "next":
                $change = 1;
                break;

            case "last":
            case "previous":
                $change = -1;
                break;
        }

        switch ($unit) {
            case "month":
                $m += $change;
                if ($m > 12) {
                    $y += floor($m / 12);
                    $m = $m % 12;
                } elseif ($m < 1) {
                    $y += ceil($m / 12) - 1;
                    $m = $m % 12 + 12;
                }
                break;

            case "year":
                $y += $change;
                break;
        }

        $this->setDate($y, $m, $d);

        return '';
    }

    /**
     * Resets the current date of the object.
     *
     * @param  integer  $year
     * @param  integer  $month
     * @param  integer  $day
     *
     * @return IntlDateTime The modified DateTime.
     */
    #[ReturnTypeWillChange] public function setDate($year, $month, $day): static
    {
        $this->set("$year/$month/$day ".$this->format('HH:mm:ss'), null, 'yyyy/MM/dd HH:mm:ss');

        return $this;
    }
}
