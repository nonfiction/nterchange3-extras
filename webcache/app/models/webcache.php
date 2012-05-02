<?php
require_once 'n_model.php';

class Webcache extends NModel {
	function __construct() {
		$this->__table = 'webcache';
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
    if ($data = $this->get($url, $ttl)) { return $data; }

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
   * Get a cached record from the database, or not.
   *
   * @param  string  $key  The key for the record, pick your schema
   * @param  int     $ttl  The time-to-live for this record, in minutes
   * @return mixed   The data, or false if the record is expired or doesn't exist
   */
  function get($key, $ttl=30){
    $now = new Date();
    if ($this->find(array('conditions'=>"`cms_headline` = '".$key."'"))){
      $this->fetch();
      $ttl_expiry = new Date($this->mtime);
      $ttl_expiry->addSeconds(60*$ttl);

      if ($ttl_expiry->getTime() > $now->getTime()) { return $this->data; }
    }
    return false;
  }
  
  /**
   * Set a key/value pair in the database
   *
   * @param string $key
   * @param string $value
   */
  function set($key, $value){
    $now = new Date();
    $this->reset();
    if ($this->find(array('conditions'=>"`cms_headline` = '".$key."'"))){
      $this->fetch();
      $this->mtime = $now->getDate();
      $this->data = $value;
      $this->update();
    } else {
      $this->cms_headline = $key;
      $this->mtime = $now->getDate();
      $this->data = $value;
      $this->insert();
    }
  }
  
}