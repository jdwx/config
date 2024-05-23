<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use InvalidArgumentException;
use JDWX\Param\IParameter;
use JDWX\Param\Parameter;


class ConfigDB implements IConfig {


    private array $rConfig;
    private array $rDefaults;
    private array $rOverrides = [];
    private array $rMerged;


    public function __construct( IParser $i_config, ?IParser $i_defaults = null ) {
        $this->rConfig = $i_config->parse();
        $this->rDefaults = $i_defaults?->parse() ?? [];
        if ( $this->rDefaults ) {
            $this->merge();
        } else {
            $this->rMerged = $this->rConfig;
        }
    }


    public function get( ... $i_rstKeys ) : IParameter {
        $r = $this->getBranch( $i_rstKeys, true );
        if ( ! $r instanceof IParameter ) {
            throw new KeyIsSectionException( self::soFar( $i_rstKeys ) );
        }
        return $r;
    }


    /** @return IParameter[] */
    public function getSection( ... $i_rstKeys ) : array {
        $r = $this->getBranch( $i_rstKeys, true );
        if ( ! is_array( $r ) ) {
            throw new KeyNotSectionException( self::soFar( $i_rstKeys ) );
        }
        $rOut = [];
        foreach ( $r as $stKey => $x ) {
            # We don't allow you to pull sections if they contain subsections.
            if ( ! $x instanceof IParameter ) {
                throw new KeyIsSectionException( self::soFar( $i_rstKeys ) . "[{$stKey}] is a subsection" );
            }
            $rOut[ $stKey ] = $x;
        }
        return $rOut;
    }


    public function hasKey( ... $i_rstKeys ) : bool {
        $r = $this->getBranch( $i_rstKeys, false );
        return $r instanceof IParameter;
    }


    public function hasSection( ... $i_rstKeys ) : bool {
        $r = $this->getBranch( $i_rstKeys, false );
        return is_array( $r );
    }


    public function set( ... $i_rstWhere ) : void {
        $pWhat = array_pop( $i_rstWhere );
        if ( ! $pWhat instanceof IParameter ) {
            $pWhat = new Parameter( $pWhat );
        }
        $stLastKey = array_pop( $i_rstWhere );
        if ( ! is_string( $stLastKey ) ) {
            throw new InvalidArgumentException( "No key" );
        }
        $r = &$this->rOverrides;
        foreach ( $i_rstWhere as $stKey ) {
            assert( is_string( $stKey ) );
            if ( ! array_key_exists( $stKey, $r ) ) {
                $r[ $stKey ] = [];
            }
            $r = &$r[ $stKey ];
        }
        $r[ $stLastKey ] = $pWhat;
        $this->merge();
    }


    public function testGet( ... $i_rstKeys ) : ?IParameter {
        $r = $this->getBranch( $i_rstKeys, false );
        if ( is_array( $r ) ) {
            throw new KeyIsSectionException( self::soFar( $i_rstKeys ) );
        }
        return $r;
    }


    public static function fromFile( string $i_stFileName, ?IParser $i_defaults = null,
                                     bool $i_bMissingIsFatal = true ) : ConfigDB {
        if ( ! file_exists( $i_stFileName ) ) {
            if ( $i_bMissingIsFatal || ! $i_defaults instanceof IParser ) {
                throw new InvalidArgumentException( "Config file not found: {$i_stFileName}" );
            }
            return new ConfigDB( $i_defaults );
        }
        $pathInfo = pathinfo( $i_stFileName );
        $ext = $pathInfo[ 'extension' ] ?? "";
        $parser = match ( $ext ) {
            'ini' => IniParser::fromFile( $i_stFileName ),
            'json' => JsonParser::fromFile( $i_stFileName ),
            'yaml' => YamlParser::fromFile( $i_stFileName ),
            default => throw new InvalidArgumentException( "Unknown file extension: {$ext}" ),
        };
        return new ConfigDB( $parser, $i_defaults );
    }


    public static function fromIniString( string $i_stIni, ?IParser $i_defaults = null ) : ConfigDB {
        return new ConfigDB( IniParser::fromString( $i_stIni ), $i_defaults );
    }


    public static function fromJsonString( string $i_stJson, ?IParser $i_defaults = null ) : ConfigDB {
        return new ConfigDB( JsonParser::fromString( $i_stJson ), $i_defaults );
    }


    public static function fromYamlString( string $i_stYaml, ?IParser $i_defaults = null ) : ConfigDB {
        return new ConfigDB( YamlParser::fromString( $i_stYaml ), $i_defaults );
    }


    private function getBranch( array $i_rstKeys, bool $i_bMissingIsFatal ) : array|IParameter|null {
        $r = $this->rMerged;
        $rSoFar = [];
        foreach ( $i_rstKeys as $stKey ) {
            $rSoFar[] = $stKey;
            if ( ! is_array( $r ) ) {
                throw new KeyNotSectionException( self::soFar( $rSoFar ) );
            }
            if ( ! array_key_exists( $stKey, $r ) ) {
                if ( $i_bMissingIsFatal ) {
                    throw new KeyNotFoundException( self::soFar( $rSoFar ) );
                }
                return null;
            }
            $r = $r[ $stKey ];
        }
        return $r;
    }


    private function merge() : void {
        $this->rMerged = $this->rDefaults;
        $this->mergeArray( $this->rMerged, $this->rConfig );
        $this->mergeArray( $this->rMerged, $this->rOverrides );
    }


    private function mergeArray( array & $io_rMerged, array $i_rNew ) : void {
        foreach ( $i_rNew as $stKey => $x ) {
            if ( is_array( $x ) ) {
                if ( ! array_key_exists( $stKey, $io_rMerged ) ) {
                    $io_rMerged[ $stKey ] = [];
                }
                if ( ! is_array( $io_rMerged[ $stKey ] ) ) {
                    throw new KeyNotSectionException( $stKey );
                }
                $this->mergeArray( $io_rMerged[ $stKey ], $x );
            } elseif ( array_key_exists( $stKey, $io_rMerged ) && is_array( $io_rMerged[ $stKey ] ) ) {
                throw new KeyIsSectionException( $stKey );
            } else {
                $io_rMerged[ $stKey ] = $x;
            }
        }
    }


    public function serialize( IFormatter $i_formatter ) : void {
        $i_formatter->format( $this->rMerged );
    }


    public function serializeToFile( string $i_stFileName ) : void {
        $pathInfo = pathinfo( $i_stFileName );
        $ext = $pathInfo[ 'extension' ] ?? "";
        $fwt = new FileWriter( $i_stFileName );
        $parser = match ( $ext ) {
            'ini' => new IniFormatter( $fwt ),
            'json' => new JsonFormatter( $fwt ),
            'yaml' => new YamlFormatter( $fwt ),
            default => throw new InvalidArgumentException( "Unknown file extension: $ext" ),
        };
        $this->serialize( $parser );
    }


    private static function soFar( array $i_rSoFar ) : string {
        $stOut = '';
        $stLast = array_pop( $i_rSoFar );
        foreach ( $i_rSoFar as $st ) {
            $stOut .= "[{$st}]";
        }
        $stOut .= $stLast;
        return $stOut;
    }


}
