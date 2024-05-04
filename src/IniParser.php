<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use RuntimeException;


readonly class IniParser extends BaseParser {


    public function parse() : array {
        $st = $this->reader->read();
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $rIni = @parse_ini_string( $st, true, INI_SCANNER_RAW );
        if ( ! is_array( $rIni ) ) {
            throw new RuntimeException( 'Failed to parse INI content' );
        }
        return $this->parseArray( $rIni );
    }


    public static function fromFile( string $i_stFileName, bool $i_bNullMeansNull = true ) : IniParser {
        return new IniParser( new FileReader( $i_stFileName ), $i_bNullMeansNull );
    }


    public static function fromString( string $i_st, bool $i_bNullMeansNull = true ) : IniParser {
        return new IniParser( new StringReader( $i_st ), $i_bNullMeansNull );
    }


}
