<?php


declare( strict_types = 1 );


use JDWX\Config\FileReader;
use PHPUnit\Framework\TestCase;


class FileReaderTest extends TestCase {


    public function testRead() : void {
        $stFileName = __DIR__ . '/data/test.ini';
        $reader = new FileReader( $stFileName );
        $stData = file_get_contents( $stFileName );
        $this->assertSame( $stData, $reader->read() );
    }


}
