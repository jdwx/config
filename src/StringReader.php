<?php


declare( strict_types = 1 );


namespace JDWX\Config;


readonly class StringReader implements IReader {


    public function __construct( private string $stData ) {

    }


    public function read() : string {
        return $this->stData;
    }


}

