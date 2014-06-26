array-access
============
[![Travis CI](https://travis-ci.org/petrgrishin/array-access.png "Travis CI")](https://travis-ci.org/petrgrishin/array-access)
[![Coverage Status](https://coveralls.io/repos/petrgrishin/array-access/badge.png?branch=master)](https://coveralls.io/r/petrgrishin/array-access?branch=master)

PHP multi array access

Installation
------------
Add a dependency to your project's composer.json file if you use [Composer](http://getcomposer.org/) to manage the dependencies of your project:
```json
{
    "require": {
        "petrgrishin/array-access": "~2.0"
    }
}
```

Usage examples
--------------
#### Basic usage array-access objects
```php
use \PetrGrishin\ArrayAccess\ArrayAccess;

$arrayParams = array(
    'a' => array(
        'b' => 10,
    )
);
$params = ArrayAccess::create($arrayParams);
$value = $params->getValue('a.b');
$params
    ->setValue('a.b', 20)
    ->setValue('a.c', 30);
$params->remove('a.b');
$resultArrayParams = $params->getArray();
// array(
//     'a' => array(
//         'c' => 30,
//     )
// )
```

#### Use ArrayMap
```php
$arrayAccess = ArrayAccess::create($array);
$arrayAccess->getMap()
    ->filter($callback)
    ->map($callback)
    ->userSortByValue($callback)
    ->userSortByKey($callback)
    ->mergeWith($array)
    ->replaceWith($array);
$resultArrayParams = $params->getArray();
```
Example of usage ArrayMap class — https://github.com/petrgrishin/array-map

#### Example of usage in Yii2 behavior
https://github.com/petrgrishin/yii2-array-field
