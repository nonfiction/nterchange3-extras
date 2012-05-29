<?php

set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.'/lib/');
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * Connect to and manipulate a Google spreadsheet, primarily in list-view mode
 * 
 * $ss = new GSpreadsheet('My Spreadsheet', 'My Worksheet', 'user@gmail.com', 'password');
 * $ss->find('active = 1');
 * $rows = $ss->fetchAll();
 */
class GSpreadsheet {
  
  // The Zend_Gdata_Spreadsheets service connection.
  public  $service;

  //A Zend_Gdata_Spreadsheets_ListFeed initialized in calls to find()
  public  $listFeed;
  
  // String names of the spreadsheet and worksheet
  private $spreadsheet_name, $worksheet_name;
  
  // Zend_Gdata_Spreadsheets_SpreadsheetEntry for the connected spreadsheet
  private $spreadsheet;
  
  // Zend_Gdata_Spreadsheets_WorksheetEntry for the connected worksheet
  private $worksheet;
  
  // String id's of the spreadsheet and worksheet
  private $ssid, $wsid;
  
  // Credentials used to connect
  private $user, $password;
  
  // String used in calls to find()
  private $user_query = null;
  
  function __construct($spreadsheet, $worksheet, $user, $password) {
    $this->spreadsheet_name = $spreadsheet;
    $this->worksheet_name = $worksheet;
    $this->user = $user;
    $this->password = $password;

    $this->data = new stdClass();
    $err = $this->getWorksheet();
    if ($err) die ($err);
  }
  
  /**
   * Accessor method for private instance variables
   * @return array
   */
  function getAttributes(){
    return array(
      'spreadsheet_name'  => $this->spreadsheet_name,
      'worksheet_name'    => $this->worksheet_name,
      'ssid'              => $this->ssid,
      'wsid'              => $this->wsid
    );
  }
  
  /**
   * Find rows in the spreadsheet
   * @param  $q string|null  - the search string
   * @return int  - number of rows found
   */
  function find($q=null){
    if ($q && is_string($q)) $this->user_query = $q;
    
    $query = new Zend_Gdata_Spreadsheets_ListQuery();
    $query->setSpreadsheetKey($this->ssid);
    $query->setWorksheetId($this->wsid);

    if ($this->user_query) $query->setSpreadsheetQuery($this->user_query);

    try {
      $this->listFeed = $this->service->getListFeed($query);
    } catch (Exception $e) {
      return 0;
    }
    return (int)$this->listFeed->getTotalResults()->text;
  }

  /**
   * Append a row to the spreadsheet
   */
  function insert(){
    $entryResult = $this->service->insertRow($this->data, $this->ssid, $this->wsid);
    return basename($entryResult->id);
  }
  
  /**
   * Return all rows
   */
  function fetchAll(){
    if (!$this->listFeed) return array();

    foreach ($this->listFeed as $i){
      $entry = array();
      $entry['id'] = basename($i->id->text);
      $entry['data'] = array();
      foreach ($i->getCustom() as $c) {
        $id = $c->rootElement;
        $entry['data'][$id] = $c->text;
      }
      $entries[] = $entry;
    }
    return $entries;
  }
  
  private function getWorksheet(){
    try {
      // connect to API
      $service_name = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
      $client = Zend_Gdata_ClientLogin::getHttpClient($this->user, $this->password, $service_name);
      $this->service = new Zend_Gdata_Spreadsheets($client);
      // get list of available spreadsheets
      $feed = $this->service->getSpreadsheetFeed();
    } catch (Exception $e) {
      return 'ERROR: ' . $e->getMessage();
    }

    foreach($feed as $entry) {
      if ($entry->title->text == $this->spreadsheet_name) {
        $spreadsheet = $entry;
        $this->ssid = basename($entry->id);
      }
    }
    if (!isset($spreadsheet)) return "couldn't connect to spreadsheet";
    $this->spreadsheet = $spreadsheet;

    foreach ($spreadsheet->getWorksheets() as $ws){
      if ($ws->title->text == $this->worksheet_name) {
        $worksheet = $ws;
        $this->wsid = basename($ws->id);
      }
    }
    if (!isset($worksheet)) return "couldn't connect to worksheet";
    
    $this->worksheet = $worksheet;
    
    return null;

  }
}

/**
 * Create a single row for entry into a spreadsheet
 * @param $fields array - The allowable column names
 * @param $source array - The values that you want to add
 */
class GSpreadsheetRow {
  private $fields;
  private $data;

  function __construct($fields=array(), $source=array()) {
    $this->fields = $fields;
    $this->data = new stdClass();
 
    foreach ($this->fields as $f) {
      $key = self::makeColumnHeader($f);

      if (!(isset($source[$f]) && $source[$f]))
      { $this->data->$key = null; }
      else
      { $this->data->$key = $source[$f]; }
    }
  }
  
  /**
   * @return stdClass - The data for the row
   */
  function data(){
    return $this->data;
  }

  /**
   * Make a Google Spreadsheet column header.
   *
   * Strip spaces, underscores and lowercase the string
   * @param $title string
   * @returns string
   */
  static function makeColumnHeader($title) {
    $key = strtolower($title);
    $key = str_replace(' ', '', $key);
    $key = str_replace('_', '', $key);
    return $key;
  }
}

?>