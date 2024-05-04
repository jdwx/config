<?php


declare( strict_types = 1 );


use JDWX\Config\FileReader;
use JDWX\Config\IniParser;
use JDWX\Config\StringReader;
use PHPUnit\Framework\TestCase;


class IniParserTest extends TestCase {


    public function testParseForFlatFile() : void {
        $rdr = new StringReader( <<<EOF
foo = bar
baz = qux
EOF
        );
        $parser = new IniParser( $rdr );
        $r = $parser->parse();
        self::assertIsArray( $r );
        self::assertCount( 2, $r );
        self::assertSame( 'bar', $r[ 'foo' ]->asString() );
        self::assertSame( 'qux', $r[ 'baz' ]->asString() );
    }


    public function testParseForSectionedFile() : void {
        $rdr = new FileReader( __DIR__ . '/data/test.ini' );
        $parser = new IniParser( $rdr );
        $r = $parser->parse();
        self::assertIsArray( $r );
        self::assertCount( 2, $r );
        self::assertArrayHasKey( 'foo', $r );
        self::assertArrayHasKey( 'qux', $r );
        self::assertIsArray( $r[ 'foo' ] );
        self::assertIsArray( $r[ 'qux' ] );
        self::assertCount( 1, $r[ 'foo' ] );
        self::assertCount( 1, $r[ 'qux' ] );
        self::assertSame( 'baz', $r[ 'foo' ][ 'bar' ]->asString() );
        self::assertSame( 'corge', $r[ 'qux' ][ 'quux' ]->asString() );
    }


    public function testParseForMixedFile() : void {
        $rdr = new StringReader( <<<EOF
foo = bar
[baz]
qux = aaa
EOF
        );
        $parser = new IniParser( $rdr );
        $r = $parser->parse();
        self::assertIsArray( $r );
        self::assertCount( 2, $r );
        self::assertArrayHasKey( 'foo', $r );
        self::assertArrayHasKey( 'baz', $r );
        self::assertIsArray( $r[ 'baz' ] );
        self::assertCount( 1, $r[ 'baz' ] );
        self::assertSame( 'bar', $r[ 'foo' ]->asString() );
        self::assertSame( 'aaa', $r[ 'baz' ][ 'qux' ]->asString() );
    }


    public function testParseForNull() : void {
        $parser = IniParser::fromString( "foo=null\n" );
        $r = $parser->parse();
        self::assertIsArray( $r );
        self::assertCount( 1, $r );
        self::assertTrue( $r[ 'foo' ]->isNull() );
    }


    public function testParseForQuotedNull() : void {
        $parser = IniParser::fromString( "foo=\"null\"\n", false );
        $r = $parser->parse();
        self::assertIsArray( $r );
        self::assertCount( 1, $r );
        self::assertSame( 'null', $r[ 'foo' ]->asString() );
    }


    public function testParseForInvalid() : void {
        $parser = IniParser::fromString( "I am not an .ini file!\n" );
        self::expectException( RuntimeException::class );
        $parser->parse();
    }


}
