<?php namespace Vinelab\Youtube\Contracts;

interface VideoInterface {
    
    /**
     * Istantiate an instance of the Video class
     * @param  StdClass $response
     * @return Vinelab\Youtube\Video           
     */
    public function make( $response );
}