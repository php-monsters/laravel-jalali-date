<?php

namespace PhpMonsters\Zaman\Helpers;

use Exception;
use PhpMonsters\Zaman\IntlDatetime;

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
     * @param  string  $date
     * @param  string  $format
     * @param  string  $locale
     * @param  string|null  $timezone
     * @return string
     * @throws Exception
     * @link http://userguide.icu-project.org/formatparse/datetime
     */
    public function gToj(
        mixed $date,
        string $format = 'yyyy/MM/dd H:m:s',
        string $locale = 'fa',
        string $timezone = null
    ): string {
        $date = new IntlDatetime($date, $timezone, 'gregorian');
        $date->setCalendar('persian');
        $date->setLocale($locale);

        return $date->format($format);
    }

    /**
     * Jalali to Gregorian
     *
     * @param  mixed  $date
     * @param  string  $format
     * @param  string  $inputLocale
     * @param  string  $locale
     * @param  string  $timezone
     * @return string
     * @throws Exception
     * @link http://userguide.icu-project.org/formatparse/datetime
     */
    public function jTog(
        string $date,
        string $format = 'yyyy/MM/dd H:m:s',
        string $inputLocale = 'fa',
        string $locale = 'en',
        string $timezone = 'Asia/Tehran'
    ): string {
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
    public function moment(string $timestamp): string
    {
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $diff = time() - $timestamp;
        if ($diff === 0) {
            return 'اکنون';
        }

        $dayDiff = (int) floor($diff / 86400);

        if ($diff > 0) {
            if ($dayDiff === 0) {
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
            if ($dayDiff === 1) {
                return 'دیروز';
            }
            if ($dayDiff < 7) {
                return $dayDiff.' روز قبل';
            }
            if ($dayDiff < 31) {
                return ceil($dayDiff / 7).' هفته قبل';
            }
            if ($dayDiff < 60) {
                return 'ماه گذشته';
            }

            return date('F Y', $timestamp);
        }

        $diff = abs($diff);
        if ($dayDiff === 0) {
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
        if ($dayDiff === 1) {
            return 'فردا';
        }
        if ($dayDiff < 4) {
            return date('l', $timestamp);
        }
        if ($dayDiff < 7 + (7 - date('w'))) {
            return 'هفته بعد';
        }
        if (ceil($dayDiff / 7) < 4) {
            return 'در '.ceil($dayDiff / 7).' هفته';
        }
        if ((int) date('n', $timestamp) === (int) date('n') + 1) {
            return 'ماه بعد';
        }

        return date('F Y', $timestamp);
    }

    /**
     * @param  string  $timestamp  timestamp
     *
     * @return false|string
     */
    public function momentEn(string $timestamp): bool|string
    {
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $diff = time() - $timestamp;
        if ($diff === 0) {
            return 'now';
        }

        $dayDiff = (int) floor($diff / 86400);

        if ($diff > 0) {
            if ($dayDiff === 0) {
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
            if ($dayDiff === 1) {
                return 'Yesterday';
            }
            if ($dayDiff < 7) {
                return $dayDiff.' days ago';
            }
            if ($dayDiff < 31) {
                return ceil($dayDiff / 7).' weeks ago';
            }
            if ($dayDiff < 60) {
                return 'last month';
            }

            return date('F Y', $timestamp);
        }

        $diff = abs($diff);
        if ($dayDiff === 0) {
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
        if ($dayDiff === 1) {
            return 'Tomorrow';
        }
        if ($dayDiff < 4) {
            return date('l', $timestamp);
        }
        if ($dayDiff < 7 + (7 - date('w'))) {
            return 'next week';
        }
        if (ceil($dayDiff / 7) < 4) {
            return 'in '.ceil($dayDiff / 7).' weeks';
        }
        if ((int) date('n', $timestamp) === (int) date('n') + 1) {
            return 'next month';
        }

        return date('F Y', $timestamp);
    }
}
