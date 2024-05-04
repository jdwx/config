<?php


declare( strict_types = 1 );

use JDWX\Config\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase {


    public function testParse() : void {

        $r = JsonParser::fromString( '{"foo": {"bar": "baz"}, "qux": {"quux": "corge"}}' )->parse();

        self::assertIsArray( $r );
        self::assertArrayHasKey( "foo", $r );
        self::assertArrayHasKey( "qux", $r );
        self::assertIsArray( $r[ "foo" ] );
        self::assertIsArray( $r[ "qux" ] );
        self::assertArrayHasKey( "bar", $r[ "foo" ] );
        self::assertArrayHasKey( "quux", $r[ "qux" ] );
        self::assertSame( "baz", $r[ "foo" ][ "bar" ]->asString() );
        self::assertSame( "corge", $r[ "qux" ][ "quux" ]->asString() );

    }


    public function testParseForInvalid() : void {
        self::expectException( RuntimeException::class );
        JsonParser::fromString( "foo" )->parse();
    }


    public function testParseForFile() : void {
        $r = JsonParser::fromFile( __DIR__ . "/data/test.json" )->parse();
        self::assertIsArray( $r );
        self::assertArrayHasKey( "foo", $r );
        self::assertArrayHasKey( "qux", $r );
        self::assertIsArray( $r[ "foo" ] );
        self::assertIsArray( $r[ "qux" ] );
        self::assertArrayHasKey( "bar", $r[ "foo" ] );
        self::assertArrayHasKey( "quux", $r[ "qux" ] );
        self::assertSame( "baz", $r[ "foo" ][ "bar" ]->asString() );
        self::assertSame( "corge", $r[ "qux" ][ "quux" ]->asString() );
    }


}