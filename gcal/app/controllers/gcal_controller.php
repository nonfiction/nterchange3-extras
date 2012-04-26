<?php
require_once 'app/controllers/asset_controller.php';
class GcalController extends AssetController {
	function __construct() {
		$this->name = 'gcal';
		$this->versioning = true;
		$this->base_view_dir = ROOT_DIR;
		parent::__construct();
	}
	
  function getFullCalendar($parameter) {
    $logging = false;
    if ($logging) $starttime = microtime(true);
    
    // See following for Calendar Data API reference
    // http://code.google.com/apis/calendar/data/2.0/reference.html#Parameters
    $calendarURL = "https://www.google.com/calendar/feeds/stampedeshowband%40gmail.com/public/basic";
    $calendarURL .= "?singleevents=true";
    $calendarURL .= "&orderby=starttime";
    $calendarURL .= "&sortorder=ascending";
    $calendarURL .= "&maxresults=180";
    $calendarURL .= "&start-min=" . date('c', strtotime("-1 months"));
    $calendarURL .= "&start-max=" . date('c', strtotime("+3 months"));
    
    $timestamp = $this->getHeaders($calendarURL);
    $model = $this->getDefaultModel();
    $model->find(); $model->fetch();
    $cached_cal = $model->toArray();

    // If stored calendar not up to date...
    if (!$cached_cal["timestamp"] || ($cached_cal["timestamp"] < $timestamp)){
      if ($logging) echo "<br>Getting new content. ";
      $xml = $this->getXML($calendarURL);
		  $events = $this->parseXMLcalendar($xml);
		  
      if ($events){
        $gcal_content = serialize($events);
        // Store the calendar in the DB
        $model->exec("DELETE from `gcal` where id=1");
        $model->gcal_content = $gcal_content;
        $model->id = 1;
        $model->timestamp = $timestamp;
        $model->cms_headline = "gCal";
        $model->cms_modified_by_user = 0;
        $model->cms_created = date("Y-m-d H:i:s");
        $model->cms_modified = date("Y-m-d H:i:s");
        $model->insert();
      }
    }
    else {
      if ($logging) echo "<br>Got cached content";
      $events = unserialize($cached_cal["gcal_content"]);
    }
    
    $cal = $this->buildHTMLCalendar($events);
    $this->set('calendarmonths', $cal);
    $html = $this->render(array('action' => 'default', 'return' => 'true'));
    
    echo $html;

    if ($logging) $endtime = microtime(true);
    if ($logging) echo "<br>Full request time: " . ($endtime - $starttime);
  }
  
  function parseXMLcalendar($xml){
    // XML Parser
    global $contentTag, $titleTag, $entryTag, $anEvent, $events;
    $events = array();
    
    // XML start tag handler
    function startTag($parser, $data){
      global $contentTag, $entryTag, $titleTag, $anEvent;
      
      if ($entryTag){
        if ($data == "CONTENT"){ $contentTag = TRUE;}
        if ($data == "TITLE")  { $titleTag = TRUE;}
      }
      if ($data == "ENTRY") { $entryTag = TRUE; $anEvent = array();}
    }
    // XML tag contents handler
    function tag_contents($parser, $data){
      global $contentTag, $entryTag, $titleTag, $anEvent;
      if ($entryTag){
        if ($contentTag == TRUE){
          //$cal_data .= $data;
          // When: Tue 26 Apr 2011
          preg_match('/When: [a-zA-Z]* (\d{1,2} [a-zA-Z]* \d{4})/', $data, $dates);
          preg_match('/Event Description: (.*)/', $data, $description); 
          if ($dates){
            $anEvent["datetime"] = strtotime($dates[1]);
            $anEvent["monthYear"] = date( "F Y", strtotime($dates[1]));
            $anEvent["month"] = date( "F", strtotime($dates[1]));
            $anEvent["year"] = date( "Y", strtotime($dates[1]));
            $anEvent["day"] = date( "d", strtotime($dates[1]));
          }
          if ($description) {
            $anEvent["description"] = $description[1];
            $anEvent["content"] = $data;
          }
        }
        if ($titleTag == TRUE){
          $anEvent["title"] = $data;
        }
        isset($anEvent["datetime"]) ? 0 : $anEvent["datetime"] = 1;
        isset($anEvent["monthYear"]) ? 0 : $anEvent["monthYear"] = 1;
      }
    }
    // XML end tag handler
    function endTag($parser, $data){
      global $contentTag, $entryTag, $cal_data, $titleTag, $anEvent, $events;
      if ($entryTag){
        if ($data == "CONTENT"){ $contentTag = FALSE;}
        if ($data == "TITLE")  { $titleTag = FALSE;}
        if ($data == "ENTRY") { $entryTag = FALSE; $events[] = $anEvent; }
      }
    }
    
    // Parse the XML into the $events array
    $xml_parser = xml_parser_create();
    xml_set_element_handler($xml_parser, "startTag", "endTag" );
    xml_set_character_data_handler($xml_parser, "tag_contents");
    xml_parse($xml_parser, $xml);
    
    return $events;
  }
  
  function buildHTMLCalendar($events){
    // Build a full calendar to encompass all events
    // returns an array of HTML months using template: calendarmonth.html
    
    $thisMonth =  (int)date('m', $events[0]["datetime"]);
    $thisYear = (int)date('Y', $events[0]["datetime"]);
    $lastMonth =  (int)date('m', $events[count($events)-1]["datetime"]);
    $lastYear = (int)date('Y',  $events[count($events)-1]["datetime"]);
    $cal = array();
    $localMonth = date('F Y', mktime());
    $monthID = 0;
    
    while ($thisYear <= $lastYear){
      while ($thisMonth < 12){
        $monthYear = date('F Y',mktime(0,0,0,$thisMonth,1,$thisYear));        
        $this->set('calendar', $this->buildMonth($thisMonth, $thisYear, $events));
        $this->set('month_year', $monthYear);
        $this->set('monthID', $monthID);
        $this->set('current', ($monthYear == $localMonth) ? ' current' : '');
        $cal[] = $this->render(array('action' => "calendarmonth", 'return' => 'true'));
        $thisMonth ++; $monthID++;
        if ($thisMonth > $lastMonth && $thisYear == $lastYear) break;
      }
      $thisYear++;
      $thisMonth = 1;
    }
    return $cal;
  }
  
  function buildMonth($month, $year, $events){
    
    // Get any events in the current month
    $thisMonthYear = date('F Y', mktime(0,0,0,$month,1,$year));
    $currentEvents = array();
    foreach ($events as $event){
      if ($event['monthYear'] == $thisMonthYear) $currentEvents[] = $event;
    }
    
    $running_day = date('w',mktime(0,0,0,$month,1,$year));
    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = array();
  
    /* row for week one */
    $thisWeek = array();
    $thisMonth = array();
  
    /* print "blank" days until the first of the current week */
    for($x = 0; $x < $running_day; $x++):
      $thisWeek[] = array('class' => 'null', 'day' => '', 'events' => "");
      $days_in_this_week++;
    endfor;
  
    /* keep going with days.... */
    for($list_day = 1; $list_day <= $days_in_month; $list_day++):
      $dayEvents = array();
      $hasEvent = false;
      foreach ($currentEvents as $event){
        if ($event['day'] == $list_day){
          $dayEvents[] = $event;
          $hasEvent = true;
        }
      }
      $thisWeek[] = array('class' => 'day', 'day' => $list_day, 'events' => $dayEvents, 'hasEvent' => $hasEvent);      
        
      if($running_day == 6):
        $thisMonth[] = $thisWeek;
        unset($thisWeek);
        $running_day = -1;
        $days_in_this_week = 0;
      endif;
      $days_in_this_week++; $running_day++; $day_counter++;
    endfor;
  
    if((1 < $days_in_this_week) && ( $days_in_this_week < 8)):
      for($x = 1; $x <= (8 - $days_in_this_week); $x++):
        $thisWeek[] = array('class' => 'null', 'day' => '', 'events' => "");
      endfor;
      $thisMonth[] = $thisWeek;
    endif;

    return $thisMonth;
  }
  
  function getXML($url){
    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($ch);
		curl_close($ch);
		return $xml;
  }
  
  // Get only HTTP headers and return Last-Modified time
  function getHeaders($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILETIME, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $header = curl_exec($ch);
    $info = curl_getinfo($ch);
    $time = curl_getinfo($ch, CURLINFO_FILETIME);
    curl_close($ch);
    return $time;
  }
}
?>

