<?php


declare( strict_types = 1 );


use JDWX\Config\ConfigDB;
use JDWX\Config\IniFormatter;
use JDWX\Config\IniParser;
use JDWX\Config\KeyIsSectionException;
use JDWX\Config\KeyNotFoundException;
use JDWX\Config\KeyNotSectionException;
use JDWX\Config\StringWriter;
use JDWX\Param\Parameter;
use PHPUnit\Framework\TestCase;


class ConfigDBTest extends TestCase {


    public function testFromFileForNoFileBad() : void {
        $defaults = IniParser::fromString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::expectException( InvalidArgumentException::class );
        ConfigDB::fromFile( __DIR__ . '/data/no-such-file.ini', $defaults );
    }


    public function testFromFileForNoFileNoDefaults() : void {
        self::expectException( InvalidArgumentException::class );
        ConfigDB::fromFile( __DIR__ . '/data/no-such-file.ini', null, false );
    }


    public function testFromFileForNoFileOk() : void {
        $defaults = IniParser::fromString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/no-such-file.ini', $defaults, false );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
    }


    public function testFromIniString() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
    }


    public function testFromJsonString() : void {
        $cfg = ConfigDB::fromJsonString( '{"foo":{"bar":"baz"},"qux":{"quux":"corge"}}' );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
    }


    public function testFromYamlString() : void {
        if ( ! extension_loaded( 'yaml' ) ) {
            self::markTestSkipped( "YAML extension not loaded." );
        }
        $cfg = ConfigDB::fromYamlString( "foo:\n  bar: baz\nqux:\n  quux: corge\n" );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
    }


    public function testGet() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
    }


    public function testGetForBadKey() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyNotFoundException::class );
        $cfg->get( 'foo', 'grault' );
    }


    public function testGetForBadSection() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyNotFoundException::class );
        $cfg->get( 'grault', 'garply' );
    }


    public function testGetForKeyIsSection() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyIsSectionException::class );
        $cfg->get( 'foo' );
    }


    public function testGetForKeyNotSection() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyNotSectionException::class );
        $cfg->get( 'foo', 'bar', 'baz' );
    }


    public function testGetSection() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\ngrault=garply\n" );
        $r = $cfg->getSection( 'foo' );
        self::assertCount( 1, $r );
        self::assertSame( 'baz', $r[ 'bar' ]->asString() );
        $r = $cfg->getSection( 'qux' );
        self::assertCount( 2, $r );
        self::assertSame( 'corge', $r[ 'quux' ]->asString() );
        self::assertSame( 'garply', $r[ 'grault' ]->asString() );
        static::expectException( KeyNotSectionException::class );
        $cfg->getSection( 'foo', 'bar' );
    }


    public function testGetSectionWithNested() : void {
        $cfg = ConfigDB::fromJsonString( '{"foo":{"bar":{"baz": "qux"},"quux":"corge"}}' );
        self::expectException( KeyIsSectionException::class );
        $cfg->getSection( 'foo' );
    }



    public function testHasKey() : void {
        $cfg = ConfigDB::fromIniString( "fred = plugh\n[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::assertTrue( $cfg->hasKey( 'foo', 'bar' ) );
        self::assertTrue( $cfg->hasKey( 'qux', 'quux' ) );
        self::assertFalse( $cfg->hasKey( 'foo', 'quux' ) );
        self::assertFalse( $cfg->hasKey( 'grault', 'garply' ) );
        self::assertFalse( $cfg->hasKey( 'waldo' ) );
        self::assertTrue( $cfg->hasKey( 'fred' ) );
        self::expectException( KeyNotSectionException::class );
        $cfg->hasKey( 'foo', 'bar', 'baz' );
    }


    public function testHasSection() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\ngrault=garply\n" );
        self::assertTrue( $cfg->hasSection( 'foo' ) );
        self::assertTrue( $cfg->hasSection( 'qux' ) );
        self::assertFalse( $cfg->hasSection( 'grault' ) );
        self::expectException( KeyNotSectionException::class );
        $cfg->hasSection( 'foo', 'bar', 'baz' );
    }


    public function testKeyForDefaults() : void {
        $defaults = IniParser::fromFile( __DIR__ . '/data/test.ini' );
        $ini = IniParser::fromString( "[foo]\nbar=grault\n[garply]waldo=fred\n" );
        $cfg = new ConfigDB( $ini, $defaults );
        self::assertSame( 'grault', $cfg->get( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->get( 'qux', 'quux' )->asString() );
        self::assertSame( 'fred', $cfg->get( 'garply', 'waldo' )->asString() );
    }


    public function testKeyForDefaultsWithConflict() : void {
        $defaults = IniParser::fromString( "foo=bar\nbaz=qux\n" );
        $ini = IniParser::fromString( "[foo]quux=corge\n" );
        self::expectException( KeyNotSectionException::class );
        $cfg = new ConfigDB( $ini, $defaults );
        unset( $cfg );
    }


    public function testKeyForDefaultsWithReverseConflict() : void {
        $defaults = IniParser::fromString( "[foo]bar=baz\n" );
        $ini = IniParser::fromString( "foo=bar\\n" );
        self::expectException( KeyIsSectionException::class );
        $cfg = new ConfigDB( $ini, $defaults );
        unset( $cfg );
    }


    public function testSerialize() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        $swr = new StringWriter();
        $ini = new IniFormatter( $swr );
        $cfg->serialize( $ini );
        $stActual = $swr->get();
        $stActual = trim( preg_replace( '/\s+/m', " ", $stActual ) );
        $stExpected = file_get_contents( __DIR__ . '/data/test.ini' );
        $stExpected = trim( preg_replace( '/\s+/m', " ", $stExpected ) );
        self::assertSame( $stExpected, $stActual );
    }


    public function testSerializeToFile() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        $stFileName = tempnam( sys_get_temp_dir(), "test" ) . ".ini";
        $cfg->serializeToFile( $stFileName );
        $stActual = file_get_contents( $stFileName );
        $stActual = trim( preg_replace( '/\s+/m', " ", $stActual ) );
        $stExpected = file_get_contents( __DIR__ . '/data/test.ini' );
        $stExpected = trim( preg_replace( '/\s+/m', " ", $stExpected ) );
        self::assertSame( $stExpected, $stActual );
        unlink( $stFileName );
    }


    public function testSet() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::assertSame( 'baz', $cfg->get( 'foo', 'bar' )->asString() );
        $cfg->set( 'foo', 'bar', new Parameter( 'grault' ) );
        self::assertSame( 'grault', $cfg->get( 'foo', 'bar' )->asString() );
        $cfg->set( 'foo', 'waldo', 'garply' );
        self::assertSame( 'garply', $cfg->get( 'foo', 'waldo' )->asString() );
        $cfg->set( 'garply', 'plugh', 'fred' );
        self::assertSame( 'fred', $cfg->get( 'garply', 'plugh' )->asString() );
    }


    public function testSetForNotSection() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::expectException( KeyNotSectionException::class );
        $cfg->set( 'foo', 'bar', 'baz', 'grault' );
    }


    public function testSetForIsSection() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::expectException( KeyIsSectionException::class );
        $cfg->set( 'foo', new Parameter( 'grault' ) );
    }


    public function testSetForNoKey() : void {
        $cfg = ConfigDB::fromIniString( "[foo]\nbar=baz\n[qux]\nquux=corge\n" );
        self::expectException( InvalidArgumentException::class );
        $cfg->set( 'foo' );
    }


    public function testSerializeForMerged() : void {
        $defaults = IniParser::fromString( "[foo]bar=baz\nqux=quux" );
        $cfg = ConfigDB::fromIniString( "[foo]bar=corge\ngrault=garply\n", $defaults );
        $cfg->set( 'foo', 'waldo', 'fred' );
        $swr = new StringWriter();
        $ini = new IniFormatter( $swr );
        $cfg->serialize( $ini );
        $stActual = $swr->get();
        $stActual = trim( preg_replace( '/\s+/m', " ", $stActual ) );
        $stExpected = "[foo]\nbar = \"corge\"\nqux = \"quux\"\ngrault = \"garply\"\nwaldo = \"fred\"\n";
        $stExpected = trim( preg_replace( '/\s+/m', " ", $stExpected ) );
        self::assertSame( $stExpected, $stActual );
    }


    public function testTestGet() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::assertSame( 'baz', $cfg->testGet( 'foo', 'bar' )->asString() );
        self::assertSame( 'corge', $cfg->testGet( 'qux', 'quux' )->asString() );
        self::assertNull( $cfg->testGet( 'foo', 'grault' ) );
        self::assertNull( $cfg->testGet( 'garply', 'waldo' ) );
    }


    public function testGetForNotSection() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyNotSectionException::class );
        $cfg->testGet( 'foo', 'bar', 'baz' );
    }


    public function testTestGetForSection() : void {
        $cfg = ConfigDB::fromFile( __DIR__ . '/data/test.ini' );
        self::expectException( KeyIsSectionException::class );
        $cfg->testGet( 'foo' );
    }


}
