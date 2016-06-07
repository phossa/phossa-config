# phossa-config
[![Build Status](https://travis-ci.org/phossa/phossa-config.svg?branch=master)](https://travis-ci.org/phossa/phossa-config)
[![HHVM](https://img.shields.io/hhvm/phossa/phossa-config.svg?style=flat)](http://hhvm.h4cc.de/package/phossa/phossa-config)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa/phossa-config.svg?style=flat)](https://packagist.org/packages/phossa/phossa-config)
[![License](https://poser.pugx.org/phossa/phossa-config/license)](http://mit-license.org/)

Introduction
---

*phossa-config* is a configuration management library for PHP. The design idea
is inspired by another github project `mrjgreen/config` but with lot of more
cool features.

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

Features
---

- One central place for all config files

  ```
  config/
   |
   |____ production/
   |        |
   |        |____ host1/
   |        |       |___ redis.php
   |        |       |___ db.php
   |        |
   |        |____ db.php
   |
   |____ system.php
   |____ db.php
   |____ redis.php
  ```

- Use an [environment](#env) value, e.g. `production` or `production/host1` for
  switching between `development`, `staging` or `production`.

- Support `.php`, `.json`, `.ini` and `.xml` type of configuration files.

- [Reference](#ref) is possible, such as `${system.tmpdir}` in configuration
  file and environment file.

- On demand configuration loading (lazy loading).

- Able to load all configuration files in one shot with `$config->get(null)`

- Configuration [cache](#cache).

- Hierachy configuration structure with dot notation like `db.auth.host`.

  ```php
  // returns an array
  $db_config = $config->get('db');

  // returns a string
  $db_host = $config->get('db.auth.host');
  ```

- Both flat notation and array notation fully supported and co-exist at the
  same time.

  ```php
  // db config file
  return [
      // array notation
      'auth' => [
          'host' => 'localhost',
          'port' => 3306
      ],

      // flat notation
      'auth.user' => 'dbuser'
  ];
  ```

- Un*x shell style environment file '.env' is supported with dereferencing
  feature and magic environment values like `${__DIR__}` and `${__FILE__}`

Installation
---

Install via the [`composer`](https://getcomposer.org/) utility.

```
composer require "phossa/phossa-config=1.*"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
      "phossa/phossa-config": "1.*"
    }
}
```

Usage
---

- <a name="env"></a>Running Environment

  Usually running environment is different for different servers. A good
  practice is setting environment in a `.env` file in the installation root
  and put all configuration files in the `config/` directory.

  Sample `.env` file,

  ```php
  # debugging true|false, change to 'false' ON production server
  APP_DEBUG=true

  # App environment, change to 'prod' ON production server
  APP_ENV=dev

  # app root directory, default to current dir
  APP_ROOT=${__DIR__}

  # central configuration directory
  CONFIG_DIR=${APP_ROOT}/config
  ```

  In the `bootstrap.php` file,

  ```php
  // load environment
  (new Phossa\Config\Env\Environment())->load(__DIR__.'/.env');

  // create config object
  $config = new Phossa\Config\Config(
      getenv('CONFIG_DIR'), // loaded from .env file
      getenv('APP_ENV')     // loaded from .env file
  );

  // load all configs in one shot
  $conf_data = $config->get(null);
  ```

- <a name="group"></a>Grouping

  Configurations are grouped into groups, namely files. For example, the
  `system.php` holds all `system.*` configurations

  ```php
  // system.php
  return [
      'tmpdir' => '/usr/local/tmp',
      ...
  ];
  ```

  Later, system related configs can be retrieved as

  ```php
  $dir = $config->get('system.tmpdir');
  ```

  Or being used in other configs as [reference](#ref).

- <a name="cache"></a>Caching

  A cache pool can be passed to the config constructor to have it read all
  configs from the cache or save all configs to cache.

  ```php
  // create config object
  $config = new Phossa\Config\Config(
      dirname(__DIR__) . '/config',     // the config dir
      'staging/server2',                // config env
      'php',                            // file type
      new Phossa\Config\Cache\Cache(__DIR__ . '/cache') // cache location
  );

  // if cache exists, this will read all configs from the cache
  $conf_data = $config->get(null);

  // ...

  // write to cache
  $config->save();
  ```

  - Pros of using caching

    - Speed up. Read from one file instead of lots of configuration files.

    - [References](#ref) like `${system.tmpdir}` are done already.

  - Cons of using caching

    - Config data might be stale. need to using `$config->save()` to overwrite
      or `$cache->clear()` to clear the cache.

    - Need write permission to a cache directory.

    - Might expose your configuration if you are not careful with cache data.

- <a name="ref"></a>Reference

  References make your configuration easy to manage.

  For example, in the `system.php`

  ```php
  // group: system
  return [
      'tmpdir' => '/var/local/tmp',
      ...
  ];
  ```

  In your `cache.php` file,

  ```php
  // group: cache
  return [
      // a local filesystem cache driver
      'local' => [
          'driver' => 'filesystem',
          'params' => [
              'root_dir'   => '${system.tmpdir}/cache',
              'hash_level' => 2
          ]
      ],
      ...
  ];
  ```
- <a name="overwrite"></a>Overwriting

  If the environment is set to `production/host1`, the precedence order is,

  - `config/production/host1/db.php` over

  - `config/production/db.php` over

  - `config/config/db.php`

- <a name="api"></a>Config API

  - `get($key, $default = null)`

    `$key` is the a flat notation like `db.auth.host` or `NULL` to  get all of
    the configurations. `$default` is used if no configs found.

    Return value might be a `string` or `array` base on the `$key`.

  - `set($key, $value)`

    Set the configuration manually in this *session*. The value will **NOT**
    be reflected in any config files unless you modify config file manually.

    `$value` can be a `string` or `array`.

  - `has($key)`

    Test if `$key` exists or not. Returns a `boolean` value.

  - `save()`

    Save current full configurations into a cache.

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared ~1.0.10

License
---

[MIT License](http://mit-license.org/)
