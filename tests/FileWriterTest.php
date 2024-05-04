<?php


declare( strict_types = 1 );


use PHPUnit\Framework\TestCase;


class FileWriterTest extends TestCase {


    public function testWrite() : void {
        $stFileName = tempnam( sys_get_temp_dir(), "test" );
        $swt = new JDWX\Config\FileWriter( $stFileName );
        $stExpected = "foo_bar_baz";
        $swt->write( $stExpected );
        $stActual = file_get_contents( $stFileName );
        self::assertSame( $stExpected, $stActual );
        unlink( $stFileName );
    }


}

