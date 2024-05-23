<?php


namespace JDWX\Config;

use JDWX\Param\IParameter;


interface IReadOnlyConfig {


    public function get( ... $i_rstKeys ) : IParameter;


    public function getSection( ... $i_rstKeys ) : array;


    public function hasKey( ... $i_rstKeys ) : bool;


    public function hasSection( ... $i_rstKeys ) : bool;


    public function testGet( ... $i_rstKeys ) : ?IParameter;


}