<?php

namespace JDWX\Config;

readonly class FileWriter implements IWriter {


    public function __construct( private string $stPath ) {
    }

    public function write(string $i_stData): void {
        file_put_contents( $this->stPath, $i_stData );
    }


}
