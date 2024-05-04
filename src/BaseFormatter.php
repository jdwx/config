<?php

namespace JDWX\Config;

use JDWX\Param\IParameter;

abstract readonly class BaseFormatter implements IFormatter {


    public function __construct( protected IWriter $writer, protected bool $bQuoteStrings ) {
    }


    /**
     * Renders a config array (arrays and IParameters) down
     * to arrays and strings.
     */
    protected function prepArray(array $i_r ) : array {
        $r = [];
        foreach ( $i_r as $stKey => $rpValue ) {
            if ( is_array( $rpValue ) ) {
                $r[$stKey] = $this->prepArray( $rpValue );
            } elseif ( $rpValue instanceof IParameter ) {
                $r[$stKey] = $this->prepParameter( $rpValue );
            } else {
                $r[$stKey] = strval( $rpValue );
            }
        }
        return $r;
    }


    protected function prepParameter(IParameter $i_param ) : string {
        $st = $i_param->asStringOrNull();
        if ( $st === null ) {
            return "null";
        }
        if ( $this->bQuoteStrings ) {
            $st = str_replace( "\"", "\\\"", $st );
            $st = "\"{$st}\"";
        }
        return $st;
    }


}
