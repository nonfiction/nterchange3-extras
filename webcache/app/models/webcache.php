<?php
require_once 'n_model.php';
require_once 'Cache/Lite.php';

class Webcache extends NModel {
  var $cache_group = 'webcache';
  function __construct() {
    $this->cache = &new Cache_Lite(array('cacheDir'=>CACHE_DIR . '/ntercache/', 'lifeTime'=>8));    
    parent::__construct();
  }

  /**
   * Caching version of file_get_contents
   *
   * @param  string $url  The webpage to fetch
   * @param  int    $ttl  The time-to-live for cached versions, in minutes (default=5)
   * @return string The contents of the page.
   */
  function wget($url, $ttl=5){
    if ($data = $this->get($url, $ttl)) {
      return $data;
    }

    $data = file_get_contents($url);
    $this->set($url, $data);
    return $data;
  }

  /**
   * Grab some remote data as a parsed XML DOM tree
   *
   * @param  string $url  The webpage to fetch
   * @param  int    $ttl  The time-to-live for cached versions, in minutes (default=5)
   * @return string The XML tree of the remote file
   */
  function getXML($url, $ttl=5){
    $feed_data = &$this->wget($url, $ttl);
    $xml = simplexml_load_string($feed_data);
    return $xml;
  }

  /**
   * Get a cached record, or not.
   *
   * @param  string  $key  The key for the record, pick your schema
   * @param  int     $ttl The time-to-live for cached versions, in minutes (default=5) 
   * @return mixed   The data, or false if the record is expired or doesn't exist
   */
  function get($key, $ttl=5){
    $this->cache->setLifeTime($ttl*60);
    if ($data = $this->cache->get($key, $this->cache_group)){
      return unserialize($data);
    }
    return false;
  }

  /**
   * Set a key/value pair
   *
   * @param string $key
   * @param string $value
   */
  function set($key, $value){
    return $this->cache->save(serialize($value), $key, $this->cache_group);
  }
}