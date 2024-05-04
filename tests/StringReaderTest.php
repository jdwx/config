<?php


declare( strict_types = 1 );


use JDWX\Config\StringReader;
use PHPUnit\Framework\TestCase;


class StringReaderTest extends TestCase {


    public function testRead() : void {
        $x = new StringReader( "foo" );
        self::assertSame( "foo", $x->read() );
    }


}
