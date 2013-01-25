<?php

/**
 * 
 * returns a service object from DI-Container
 * @param string $id
 * @return object 
 */
function get_service($id)	{
  return sfContext::getInstance()->getConfiguration()->getService($id);
}