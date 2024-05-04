<?php


declare( strict_types = 1 );


namespace JDWX\Config;

use RuntimeException;

readonly class JsonParser extends BaseParser {


    public function parse() : array {
        $st = $this->reader->read();
        $r = json_decode( $st, true );
        if ( ! is_array( $r ) ) {
            throw new RuntimeException( "Failed to parse JSON." );
        }
        return $this->parseArray( $r );
    }


    public static function fromFile( string $i_stFileName, bool $i_bNullMeansNull = true ) : JsonParser {
        return new JsonParser( new FileReader( $i_stFileName ), $i_bNullMeansNull );
    }


    public static function fromString( string $i_st, bool $i_bNullMeansNull = true ) : JsonParser {
        return new JsonParser( new StringReader( $i_st ), $i_bNullMeansNull );
    }


}