<?php


declare( strict_types = 1 );


use JDWX\Config\StringWriter;
use JDWX\Param\Parameter;
use PHPUnit\Framework\TestCase;


class YamlFormatterTest extends TestCase {


    public function setUp() : void {
        if ( ! function_exists( "yaml_parse" ) ) {
            self::markTestSkipped( "YAML extension is not installed." );
        }
    }


    public function testFormat() : void {
        $r = [
            'foo' => [
                'bar' => new Parameter( 'baz' )
            ],
            'qux' => [
                'quux' => new Parameter( 'corge' )
            ]
        ];

        $swt = new StringWriter();
        $f = new JDWX\Config\YamlFormatter( $swt );
        $f->format( $r );
        $stActual = trim( $swt->get() );
        $stExpected = trim( file_get_contents( __DIR__ . "/data/test.yaml" ) );
        self::assertSame( $stExpected, $stActual );
    }


}