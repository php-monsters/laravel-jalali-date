# ZAMAN - Jalali datetime component for Laravel:
This component is based on native PHP International (php-intl) extension,
So php-intl extension must be installed on your server. 

known as:

- Hijri Shamsi
- Jalali Date
- JDatetime
- هجری شمسی
- تقویم خورشیدی
- تاریخ شمسی

Supprts Laravel 5+ and PHP 8.1+

## php-intl extension installation

- On windows servers, open your php.ini (which should be in Program Files/PHP), and simply uncomment the extension.
```
extension=php_intl.dll
```

- Debian based Linux (Debian/Ubuntu/Mint/ ...)
```
sudo apt-get install php-intl
```

- Redhat based Linux (Redhat/Centos/ ...)
```
sudo dnf -y install php-intl
```
Restart your webserver - done.

### Composer installation

```php
composer require php-monsters/laravel-jalali-date
```

### Integration with Laravel 5.*

Add package alias to your app aliases (only for Laravel < 5.5):

```php
// aliases
'Zaman' => PhpMonsters\Zaman\Facades\Zaman::class,
```

### Usage samples

```php

// Jalali to Gregorian samples
echo Zaman::jTog('next week');
echo Zaman::jTog('now');
echo Zaman::jTog('1396-06-30 05:30:10');
echo Zaman::jTog ('۱۳۹۱/۱۰/۱۲ ۲۰:۳۰:۵۵', 'yyyy/MM/dd H:m:s', 'fa', 'en', 'Asia/Tehran');

// Gregorian to Jalali samples 
echo Zaman::gToj('2 days ago');
echo Zaman::gToj('2010-10-24 22:50:14');
echo Zaman::gToj('2014-09-21 07:12:54', 'EEEE yyyy/MMMM/dd H:m:s');

// Moment samples
// JALALI moment
echo Zaman::moment(strtotime('3 hours ago'), Zaman::CAL_JALALI); // "3 ساعت قبل"
echo Zaman::momentJalali(strtotime('3 hours ago')); // "3 ساعت قبل"
echo Zaman::moment(strtotime('2017-01-02 00:10:20'), Zaman::CAL_JALALI); // "2 هفته قبل"
echo Zaman::momentJalali(strtotime('2017-01-02 00:10:20')); // "2 هفته قبل"

// GREGORIAN moment
echo Zaman::moment('now', Zaman::CAL_GREGORIAN); // "May 2017"
echo Zaman::momentGregorian(1494328806); // "May 2017"
echo Zaman::moment('1 month ago', Zaman::CAL_GREGORIAN); // "last month"
echo Zaman::momentGregorian(1494334506); // "last month"

// HIJRI moment
// ISN'T implemented yet!!!
// echo Zaman::moment('now', Zaman::CAL_HIJRI);
// echo Zaman::momentHijri(1494328806);

// Blade usage example
{{ Zaman::gToj('2011-11-20 19:12:19') }}

```

### Date/Time formats
[Supported Formats Documentation](http://userguide.icu-project.org/formatparse/datetime)

## Team

This component is developed by the following person(s) and a bunch of [awesome contributors](https://github.com/php-monsters/laravel-jalali-date/graphs/contributors).

[![Aboozar Ghaffari](https://avatars1.githubusercontent.com/u/502961?s=130&v=4)](https://github.com/samuraee) |
--- |
[Aboozar Ghaffari](https://github.com/samuraee) |


## Support this project
  
Please support the package by giving it :star: and contributing to its development.

## License

The Laravel Jalali Datetime is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
