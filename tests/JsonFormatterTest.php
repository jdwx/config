<?php


declare( strict_types = 1 );


use JDWX\Config\JsonFormatter;
use JDWX\Config\StringWriter;
use JDWX\Param\Parameter;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase {


    public function testFormat() : void {
        $swt = new StringWriter();
        $f = new JsonFormatter( $swt );
        $r = [
            "foo" => [
                'bar' => new Parameter( "baz" ),
            ],
            "qux" => [
                'quux' => new Parameter( "corge" )
            ],
        ];
        $f->format( $r );
        $stActual = $swt->get();
        $stActual = trim( preg_replace( '/\s/', '', $stActual ) );
        $stExpected = file_get_contents( __DIR__ . "/data/test.json" );
        $stExpected = trim( preg_replace( '/\s/', '', $stExpected ) );
        self::assertSame( $stExpected, $stActual );

    }


    public function testFormatForNonParameter() : void {
        $r = [
            'foo' => 5,
        ];
        $swt = new StringWriter();
        $f = new JsonFormatter( $swt );
        $f->format( $r );
        $stActual = $swt->get();
        $stActual = trim( preg_replace( '/\s/', '', $stActual ) );
        $stExpected = '{"foo":"5"}';
        self::assertSame( $stExpected, $stActual );
    }


}