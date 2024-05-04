<?php


declare( strict_types = 1 );


namespace JDWX\Config;


use Throwable;


class KeyIsSectionException extends InvalidArgumentException {


    public function __construct( string $message = "", int $code = 0, ?Throwable $previous = null ) {
        $message = "Key is a section: {$message}";
        parent::__construct( $message, $code, $previous );
    }


}
