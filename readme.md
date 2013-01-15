# Meido Str [![Build Status](https://secure.travis-ci.org/meido/str.png?branch=master)](https://travis-ci.org/meido/str)

A port of Laravel 3's Str class. Made to work with Laravel 4.

- [Usage](https://github.com/meido/str#usage)
- [Changelog](https://github.com/meido/str#usage)
- [Things To Note](https://github.com/meido/str#usage)

## Usage

### Composer Side

add `"meido/str": "1.1.*"` to the `require` section of your `composer.json` so that it should look something the code below (you can, of course, include your own dependencies)

```composer
...
...
...
"require": {
	...
	...
	...
	"meido/str": "1.1.*"
},
...
...
...
```

### Laravel Side

add the following code to the `providers` section of the `app/config/app.php` file

```php
'Meido\Str\StrServiceProvider',
```

so that it'll look something like the following

```php
'providers' => array(

	...
	...
	...
	'Meido\Str\StrServiceProvider',

),
```

and add the following code to the `aliases` section of the `app/config/app.php` file

```php
'Str' => 'Meido\Str\StrFacade',
```

so that it'll look something like the following

```php
'aliases' => array(

	...
	...
	...
	'Str'       => 'Meido\Str\StrFacade',

),
```

after that, run `composer install` and start hacking on that beast.

## Changelog

### 1.1.*
- dependencies are updated.

### 1.0.*
- tagged for stable release. (1.0.0)

## Things to note

- Pluralizers are not supported.
- `ascii` & `slug` method are not supported.