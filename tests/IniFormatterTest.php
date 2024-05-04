<?php


declare( strict_types = 1 );


use JDWX\Config\IniFormatter;
use JDWX\Config\StringWriter;
use JDWX\Param\Parameter;
use PHPUnit\Framework\TestCase;


class IniFormatterTest extends TestCase {


    public function testFormat() : void {
        $r = [
            'foo' => [
                'bar' => new Parameter( 'baz' ),
            ],
            'qux' => [
                'quux' => new Parameter( 'corge' ),
            ],
        ];
        $w = new StringWriter();
        $f = new IniFormatter( $w );
        $stExpected = "[foo]\nbar = \"baz\"\n[qux]\nquux = \"corge\"\n";
        $stExpected = trim( preg_replace( '/\s+/m', " ", $stExpected ) );
        $f->format( $r );
        $stActual = trim( preg_replace( '/\s+/m', " ", $w->get() ) );
        self::assertSame( $stExpected, $stActual );
    }


    public function testFormatForFlat() : void {
        $r = [
            'foo' => new Parameter( 'bar' ),
            'qux' => new Parameter( null ),
        ];
        $w = new StringWriter();
        $f = new IniFormatter( $w );
        $stExpected = "foo = \"bar\"\nqux = null\n";
        $stExpected = trim( preg_replace( '/\s+/m', " ", $stExpected ) );
        $f->format( $r );
        $stActual = trim( preg_replace( '/\s+/m', " ", $w->get() ) );
        self::assertSame( $stExpected, $stActual );
    }


    public function testFormatForNestedSections() : void {
        $r = [
            'foo' => [
                'bar' => new Parameter( 'baz' ),
                'qux' => [
                    'quux' => new Parameter( 'corge' ),
                ],
            ],
        ];
        $w = new StringWriter();
        $f = new IniFormatter( $w );
        self::expectException( RuntimeException::class );
        $f->format( $r );
    }


}
