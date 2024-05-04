<?php

declare( strict_types = 1 );

use JDWX\Config\ConfigDB;
use JDWX\Config\IniParser;

require 'vendor/autoload.php';

$stDefaults = <<<CFG
[foo]
bar = baz
qux = 1
CFG;

$defaults = IniParser::fromString( $stDefaults );
$cfg = ConfigDB::fromFile( __DIR__ . '/example.ini', $defaults );
echo "[foo]bar = ", $cfg->get( 'foo', 'bar' )->asString(), "\n";
echo "[foo]qux = ", $cfg->get( 'foo', 'qux' )->asInt(), "\n";
echo "[foo]grault = ", $cfg->get( 'foo', 'grault' )->asString(), "\n";
