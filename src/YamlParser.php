<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use RuntimeException;


readonly class YamlParser extends BaseParser {


    public function parse() : array {
        if ( ! function_exists( "yaml_parse" ) ) {
            throw new RuntimeException( "YAML extension is not installed." );
        }
        $st = $this->reader->read();
        $r = yaml_parse( $st );
        if ( ! is_array( $r ) ) {
            throw new RuntimeException( "Failed to parse YAML." );
        }
        return $this->parseArray( $r );
    }


    public static function fromFile( string $i_stFileName, bool $i_bNullMeansNull = true ) : YamlParser {
        return new YamlParser( new FileReader( $i_stFileName ), $i_bNullMeansNull );
    }


    public static function fromString( string $i_st, bool $i_bNullMeansNull = true ) : YamlParser {
        return new YamlParser( new StringReader( $i_st ), $i_bNullMeansNull );
    }


}

