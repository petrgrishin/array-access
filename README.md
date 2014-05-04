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
        "petrgrishin/array-access": "*"
    }
}
```

Usage examples
--------------
#### Basic usage array-access objects
```php
use \PetrGrishin\ArrayAccess\ArrayAccess;

$params = ArrayAccess::create(array(
    'a' => array(
        'b' => 10,
    )
));
$value = $params->getValue('a.b');
```