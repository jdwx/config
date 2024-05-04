<?php


declare( strict_types = 1 );


namespace JDWX\Config;

use JDWX\Param\Parameter;

abstract readonly class BaseParser implements IParser {


    public function __construct( protected IReader $reader, protected bool $bNullMeansNull = true ) {
    }


    protected function parseArray( array $i_rIni ) : array {
        $r = [];
        foreach ( $i_rIni as $stKey => $rstSection ) {
            if ( is_array( $rstSection ) ) {
                $r[ strval( $stKey ) ] = $this->parseArray( $rstSection );
            } else {
                if ( "null" === $rstSection && $this->bNullMeansNull ) {
                    $rstSection = null;
                }
                $r[ strval( $stKey ) ] = new Parameter( $rstSection );
            }
        }
        return $r;
    }


}