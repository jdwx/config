# jdwx/config

A simple PHP module for interacting with config files.

This module provides a simple interface to configuration files 
in a variety of formats:

* .ini
* .json
* .yaml

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/config
```

Or download the source from GitHub: https://github.com/jdwx/config.git

## Requirements

This library requires PHP 8.0 or later. It has not been tested for 
compatibility with earlier versions.

## Usage

```php
<?php

declare( strict_types = 1 );

use JDWX\Config\ConfigDB;
use JDWX\Config\IniParser;

require 'vendor/autoload.php';

$stDefaults = <<<CFG
[foo]
bar = baz
qux = quux
CFG;

$defaults = IniParser::fromString( $stDefaults );
$cfg = ConfigDB::fromFile( __DIR__ . '/example.ini', $defaults );
echo "[foo]bar = ", $cfg->get( 'foo', 'bar' )->asString(), "\n";
echo "[foo]qux = ", $cfg->get( 'foo', 'qux' )->asInt(), "\n";
echo "[foo]grault = ", $cfg->get( 'foo', 'grault' )->asString(), "\n";
```

## Stability

This module is stable and has been used in production code. New methods
or support for additional file formats may be added in the future, but
existing methods should not be changed in a way that breaks existing code
without at least a minor version update.

## History

This module was originally developed in 2024 to supersede and unify three
other existing modules for configuration management.
