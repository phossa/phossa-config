# phossa-config
[![Build Status](https://travis-ci.org/phossa/phossa-config.svg?branch=master)](https://travis-ci.org/phossa/phossa-config)
[![HHVM](https://img.shields.io/hhvm/phossa/phossa-config.svg?style=flat)](http://hhvm.h4cc.de/package/phossa/phossa-config)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa/phossa-config.svg?style=flat)](https://packagist.org/packages/phossa/phossa-config)
[![License](https://poser.pugx.org/phossa/phossa-config/license)](http://mit-license.org/)

Introduction
---

*phossa-config* is a PHP configuration management library which handles the
different types of configure files for projects.

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

Features
---

- Support PHP and ENV type configuration files.

- Configuration cache for speed.

- Configuration tagging support for different running environment.

- Parameter reference '${SYSTEM_TMPDIR}' support. Dereferencing at run time.

  - '.' notation or '_' notation configurable

  - DATABASE_${ENV}_HOST: base one ${ENV} either 'DEV' or 'PROD'

- Configuration provider support.

- '.env' configuration file support

  - validation

  - immutability

  - referencing/nesting

  - shell support

- Different level configs

  - Environment: 'DEV', 'PROD'

  - Host base: 'SERVER1', 'SERVER2'

Getting started
---

- **Installation**

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

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared ~1.0.10

License
---

[MIT License](http://mit-license.org/)
