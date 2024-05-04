<?php


namespace JDWX\Config;


use RuntimeException;


readonly class YamlFormatter extends BaseFormatter {


    public function __construct( IWriter $i_writer ) {
        parent::__construct( $i_writer, false );
    }


    /**
     * @inheritDoc
     */
    public function format( array $i_rData ): void {
        if ( ! function_exists( "yaml_emit" ) ) {
            throw new RuntimeException( "YAML extension is not installed." );
        }
        $r = $this->prepArray( $i_rData );
        $this->writer->write( yaml_emit( $r ) );
    }
}