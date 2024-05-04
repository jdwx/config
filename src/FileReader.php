<?php


declare( strict_types = 1 );


namespace JDWX\Config;


class FileReader implements IReader {


    public function __construct( private string $stFileName ) {
    }


    public function read() : string {
        return file_get_contents( $this->stFileName );
    }


}
