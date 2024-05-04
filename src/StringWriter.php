<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use RuntimeException;


class StringWriter implements IWriter {


    private ?string $st = null;


    public function get() : string {
        if ( is_string( $this->st ) ) {
            return $this->st;
        }
        throw new RuntimeException( "No data written" );
    }


    public function write( string $i_stData ) : void {
        $this->st = $i_stData;
    }


}
