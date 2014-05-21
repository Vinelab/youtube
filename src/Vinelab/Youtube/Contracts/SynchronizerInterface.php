<?php namespace Vinelab\Youtube\Contracts;

interface SynchronizerInterface {
    
   /**
    * Sync the resources.
    * 
    * in case a resource(video, or channel) has been been deleted
    * a 'IncompatibleParametersObjectTypesException' will be thrown
    * which means that the existing data and the new one are not 
    * compatible(different kind) because if the resource has been deleted
    * Null will be returned in the response.
    * 
    * @param  Channel|Video $resource          
    * @return Channel|Video
    */
    public function sync($resource);
}