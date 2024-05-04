<?php


declare( strict_types = 1 );


use JDWX\Config\StringWriter;
use PHPUnit\Framework\TestCase;


class StringWriterTest extends TestCase {


    public function testWrite() : void {
        $sw = new StringWriter();
        $sw->write( 'foo' );
        self::assertSame( 'foo', $sw->get() );
    }


    public function testGetWithoutWrite() : void {
        $sw = new StringWriter();
        self::expectException( RuntimeException::class );
        $sw->get();
    }


}
