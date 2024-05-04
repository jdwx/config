<?php


namespace JDWX\Config;

interface IParser {


    /**
     * @return array The parsed configuration parameters.
     *
     * This is allowed to return a combination of arrays and Parameter
     * objects nested to arbitrary depth.  E.g., a flat .ini file
     * might result in a single array of Parameter objects, while a
     * nested .ini file might result in an array of arrays of Parameter
     * objects.
     */
    public function parse() : array;


}