<?php

/**
* HttpClient provides an easy interface for preforming Hyper-Text Transfer 
* Protocol (HTTP) requests. HttpClient supports some simple features expected 
* from an HTTP client, but not more complex features such as HTTP 
* cookies, authentication or file uploads.
* @package HttpClient
* @author Dominic Webb <dominic.webb@assertis.co.uk>
*/
class HttpClient
{

  private $type = null;
  private $uri = null;
  private $params = null;
  private $curl = null;


  public function __construct($uri=false)
  {
    if (isset($uri)) {
      $this->uri = $uri;
    }
    return true;
  }
  
  /**
  * Perform the actual HTTP request
  */
  public function request()
  {
  
  }

  /**
  * Set a Curl parameter
  * @param string $key 
  * @param string $val 
  */ 
  public function setParameter($key, $val)
  {
    $this->params[] = array($key, $val);
    return true; 
  }
 
  /**
  * Set a Curl parameter
  * @link php.net/manual/en/function.curl-setopt.php
  * @param string $option The CURLOPT_XXX option to set
  * @param string $value The value of the option being set
  */ 
  public function setCurlParameter($option, $value)
  {
    $this->params[] = array($option, $value);
    return true; 
  }
}
?>
