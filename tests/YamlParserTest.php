<?php


declare( strict_types = 1 );


use PHPUnit\Framework\TestCase;


class YamlParserTest extends TestCase {


    protected function setUp() : void {
        if ( ! function_exists( "yaml_parse" ) ) {
            self::markTestSkipped( "YAML extension is not installed." );
        }
    }


    public function testParse() : void {

        $r = JDWX\Config\YamlParser::fromString( "foo: bar\nqux: quux" )->parse();

        self::assertArrayHasKey( "foo", $r );
        self::assertArrayHasKey( "qux", $r );
        self::assertSame( "bar", $r[ "foo" ]->asString() );
        self::assertSame( "quux", $r[ "qux" ]->asString() );

    }


    public function testParseForInvalid() : void {
        self::expectException( RuntimeException::class );
        JDWX\Config\YamlParser::fromString( "foo" )->parse();
    }


    public function testParseForFile() : void {
        $r = JDWX\Config\YamlParser::fromFile( __DIR__ . "/data/test.yaml" )->parse();
        self::assertArrayHasKey( "foo", $r );
        self::assertArrayHasKey( "qux", $r );
        self::assertSame( "baz", $r[ "foo" ][ "bar" ]->asString() );
        self::assertSame( "corge", $r[ "qux" ][ "quux" ]->asString() );
    }


}