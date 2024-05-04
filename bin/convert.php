#!/usr/bin/env php
<?php


use JDWX\Config\ConfigDB;


require __DIR__ . "/../vendor/autoload.php";


function main( array $argv ) : int {
    $stCommand = array_shift( $argv );
    if ( count( $argv ) < 2 ) {
        echo "Usage: {$stCommand} <from> <to>\n";
        return 1;
    }
    $stFrom = array_shift( $argv );
    $stTo = array_shift( $argv );
    $cfg = ConfigDB::fromFile( $stFrom );
    $cfg->serializeToFile( $stTo );

    return 0;
}


exit( main( $argv ) );
