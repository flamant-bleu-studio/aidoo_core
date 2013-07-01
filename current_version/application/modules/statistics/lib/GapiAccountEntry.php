<?php

/**
 * Class gapiAccountEntry
 * 
 * Storage for individual gapi account entries
 *
 */

class Statistics_Lib_GapiAccountEntry
{
  private $properties = array();
  
  public function __construct($properties)
  {
    $this->properties = $properties;
  }
  
  /**
   * toString function to return the name of the account
   *
   * @return String
   */
  public function __toString()
  {
    if(isset($this->properties['title']))
    {
      return $this->properties['title'];
    }
    else 
    {
      return;
    }
  }
  
  /**
   * Get an associative array of the properties
   * and the matching values for the current result
   *
   * @return Array
   */
  public function getProperties()
  {
    return $this->properties;
  }
  
  /**
   * Call method to find a matching parameter to return
   *
   * @param $name String name of function called
   * @return String
   * @throws Exception if not a valid parameter, or not a 'get' function
   */
  public function __call($name,$parameters)
  {
    if(!preg_match('/^get/',$name))
    {
      throw new Exception('No such function "' . $name . '"');
    }
    
    $name = preg_replace('/^get/','',$name);
    
    $property_key = Statistics_Lib_Gapi::array_key_exists_nc($name,$this->properties);
    
    if($property_key)
    {
      return $this->properties[$property_key];
    }
    
    throw new Exception('No valid property called "' . $name . '"');
  }

}