<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use JDWX\Param\IParameter;
use RuntimeException;


readonly class IniFormatter extends BaseFormatter {


    public function __construct( IWriter $i_writer ) {
        parent::__construct( $i_writer, true );
    }


    /**
     * @inheritDoc
     */
    public function format( array $i_rData ) : void {
        $st = "";
        foreach ( $i_rData as $stKey => $rpValue ) {
            if ( is_array( $rpValue ) ) {
                $st .= "[$stKey]\n";
                $st .= $this->formatSection( $rpValue );
            } else {
                $stValue = $this->prepParameter( $rpValue );
                $st .= "$stKey = $stValue\n";
            }
        }
        $this->writer->write( $st );
    }


    private function formatSection( array $i_r ) : string {
        $st = "";
        foreach ( $i_r as $stKey => $rpValue ) {
            if ( ! $rpValue instanceof IParameter ) {
                throw new RuntimeException( "Ini does not support nested sections." );
            }
            $stValue = $this->prepParameter( $rpValue );
            $st .= "$stKey = {$stValue}\n";
        }
        return $st;

    }


}
