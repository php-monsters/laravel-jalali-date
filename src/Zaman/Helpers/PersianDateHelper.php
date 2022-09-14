<?php

namespace Tartan\Zaman\Helpers;

use Tartan\Zaman\IntlDatetime;

/**
 * Class PersianDateHelper
 * @package PhpMonsters\Zaman
 * @author  Aboozar Ghaffari <aboozar.ghf@gmail.com>
 */
class PersianDateHelper
{
    /**
     * Gregorian to Jalali
     *
     * @param $date
     * @param  string  $format
     * @param  string  $locale
     * @return string
     * @link http://userguide.icu-project.org/formatparse/datetime
     *
     */
    public function gToj($date, string $format = 'yyyy/MM/dd H:m:s', string $locale = 'fa', $timezone = null)
    {
        $date = new IntlDatetime($date, $timezone, 'gregorian');
        $date->setCalendar('persian');
        $date->setLocale($locale);

        return $date->format($format);
    }

    /**
     * Jalali to Gregorian
     *
     * @param $date
     * @param  string  $format
     * @param  string  $inputLocale
     * @param  string  $locale
     * @return string
     * @link http://userguide.icu-project.org/formatparse/datetime
     *
     */
    public function jTog(
        $date,
        string $format = 'yyyy/MM/dd H:m:s',
        string $inputLocale = 'fa',
        string $locale = 'en',
        $timezone = 'Asia/Tehran'
    ) {
        $date = new IntlDatetime($date, $timezone, 'persian', $inputLocale);

        $date->setCalendar('Gregorian');
        $date->setLocale($locale);

        return $date->format($format);
    }

    /**
     * @param  string  $timestamp  timestamp
     *
     * @return string
     */
    public function moment(string $timestamp)
    {
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $diff = time() - $timestamp;
        if ($diff == 0) {
            return 'اکنون';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) {
                    return 'اکنون';
                }
                if ($diff < 120) {
                    return 'یک دقیقه قبل';
                }
                if ($diff < 3600) {
                    return floor($diff / 60).' دقیقه قبل';
                }
                if ($diff < 7200) {
                    return 'یک ساعت پیش';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600).' ساعت قبل';
                }
            }
            if ($day_diff == 1) {
                return 'دیروز';
            }
            if ($day_diff < 7) {
                return $day_diff.' روز قبل';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7).' هفته قبل';
            }
            if ($day_diff < 60) {
                return 'ماه گذشته';
            }

            return date('F Y', $timestamp);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'یک دقیقه پیش';
                }
                if ($diff < 3600) {
                    return floor($diff / 60).' دقیقه پیش';
                }
                if ($diff < 7200) {
                    return 'یک ساعت پیش';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600).' ساعت پیش';
                }
            }
            if ($day_diff == 1) {
                return 'فردا';
            }
            if ($day_diff < 4) {
                return date('l', $timestamp);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'هفته بعد';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'در '.ceil($day_diff / 7).' هفته';
            }
            if (date('n', $timestamp) == date('n') + 1) {
                return 'ماه بعد';
            }

            return date('F Y', $timestamp);
        }
    }

    /**
     * @param  string  $timestamp  timestamp
     *
     * @return false|string
     */
    public function momentEn(string $timestamp)
    {
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $diff = time() - $timestamp;
        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) {
                    return 'just now';
                }
                if ($diff < 120) {
                    return '1 minute ago';
                }
                if ($diff < 3600) {
                    return floor($diff / 60).' minutes ago';
                }
                if ($diff < 7200) {
                    return '1 hour ago';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600).' hours ago';
                }
            }
            if ($day_diff == 1) {
                return 'Yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff.' days ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7).' weeks ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }

            return date('F Y', $timestamp);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'in a minute';
                }
                if ($diff < 3600) {
                    return 'in '.floor($diff / 60).' minutes';
                }
                if ($diff < 7200) {
                    return 'in an hour';
                }
                if ($diff < 86400) {
                    return 'in '.floor($diff / 3600).' hours';
                }
            }
            if ($day_diff == 1) {
                return 'Tomorrow';
            }
            if ($day_diff < 4) {
                return date('l', $timestamp);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'next week';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'in '.ceil($day_diff / 7).' weeks';
            }
            if (date('n', $timestamp) == date('n') + 1) {
                return 'next month';
            }

            return date('F Y', $timestamp);
        }
    }
}
