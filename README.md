# ZAMAN - Laravel 5+ Jalali datetime component:
This component based on PHP International (php-intl) extension so php-intl extension must be installed on your web server. 

known as:

- Hijri Shami
- Jalali Date
- JDatetime
- هجری شمسی
- تقویم خورشیدی
- تاریخ شمسی

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
sudo yum -y install php-intl
```
Restart your webserver - done.

### Composer installation

```php
composer require tartan/laravel-jalali-date
```

### Integration with Laravel 5.*

Add Zaman to app aliases in config/app.php file

```php
// aliases
'Zaman' => Tartan\Zaman\Facades\Zaman::class,
```

### Usage samples

```php

// Jalali to Gregraian samples
echo Zaman::jTog('next week');
echo Zaman::jTog('now');
echo Zaman::jTog('1396-06-30 05:30:10');
echo Zaman::jTog ('۱۳۹۱/۱۰/۱۲ ۲۰:۳۰:۵۵', 'yyyy/MM/dd H:m:s', 'fa', 'en', 'Asia/Tehran');

// Gregorian to Jalali samples 
echo Zaman::gToj('2 days ago');
echo Zaman::gToj('2010-10-24 22:50:14');
echo Zaman::gToj('2014-09-21 07:12:54', 'EEEE yyyy/MMMM/dd H:m:s');


// Moment samples
echo Zaman::moment(1494328806);
echo Zaman::moment(strtotime('3 hours ago'));
echo Zaman::moment(strtotime('2017-01-02 00:10:20'));
echo Zaman::momentEn(1494334506);

// Blade usage example
{{ Zaman::gToj('2011-11-20 19:12:19') }}

```

### Date/Time formats
[Supported Formats Documentation](http://userguide.icu-project.org/formatparse/datetime)

## Team

This component is developed by the following person(s) and a bunch of [awesome contributors](https://github.com/iamtartan/laravel-jalali-date/graphs/contributors).

[![Aboozar Ghaffari](https://avatars1.githubusercontent.com/u/502961?s=130&v=4)](https://github.com/iamtartan) |
--- |
[Aboozar Ghaffari](https://github.com/iamtartan) |


## Support this project
  
Please support the package by giving it :star: and contributing to its development.

## License

The Laravel Jalali Datetime is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
