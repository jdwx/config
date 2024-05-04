<?php


declare( strict_types = 1 );


namespace JDWX\Config;

readonly class JsonFormatter extends BaseFormatter {


    public function __construct( IWriter $i_writer ) {
        parent::__construct( $i_writer, false );
    }


    public function format( array $i_rData ) : void {
        $r = $this->prepArray( $i_rData );
        $this->writer->write( json_encode( $r, JSON_PRETTY_PRINT ) );
    }


}