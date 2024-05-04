<?php


namespace JDWX\Config;

interface IFormatter {


    /**
     * @param array $i_rData The configuration parameters to format.
     */
    public function format( array $i_rData ) : void;


}
