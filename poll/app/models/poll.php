<?php
require_once 'n_model.php';
require_once 'n_db.php';

/**
 * The Poll model primarily uses the poll table but also manages the
 * poll_values and the poll_submissions tables.
 *
 * A poll will have up to 8 poll_values associated with it
 * A poll_value will 0-n poll_submissions associated with it
 */
class Poll extends NModel {
  function __construct() {
    $this->__table = 'poll';
    $this->form_required_fields[] = 'question_count';
    $this->form_elements['cms_headline'] = array('text', 'cms_headline', 'Question' );
    $this->order_by = 'active DESC, id DESC';
    parent::__construct();
  }
  
  /**
   * Activates a poll and deactivates all others
   * @param $id int - the poll id to activate
   */
  function activate($id){
    $db = &NDB::connect();
    $db->exec("UPDATE `poll` SET `active` = 0;");
    $db->exec("UPDATE `poll` SET `active` = 1 WHERE `id` = $id;");
  }

  /**
   * Create or update the values for a poll question
   */
  function dropUpdateValues($id, $values) {
    $db = &NDB::connect();
    
    $res = $db->query("SELECT * FROM `poll_values` WHERE `poll_id` = $id;");
    while ($row = $res->fetchRow()) $results[] = $row;
    
    $i = -1;
    foreach ($values as $k=>$val) {
      $i += 1;
      $val = mysql_escape_string($val);
      
      if (array_key_exists($i, $results)){
        // Update
        $sql = "UPDATE `poll_values` SET `value` = '$val' WHERE id = {$results[$i]['id']};";
        print_r($db->exec($sql));
      }
      else {
        // Insert
        $db->exec("INSERT into `poll_values` (poll_id, value) VALUES ($id, '$val')");
      }
    }
  }
  
  /**
   * Vote on a poll
   * @param $ip string - A unique identifier for the voter [unused in favor of setting a cookie]
   * @param $value_id  - The id of the poll_values entry
   * @param $poll_id   - The id of the poll entry
   * @return bool      - Whether the vote was submitted successfully
   */
  function submitVote($ip, $value_id, $poll_id) {
    $db = &NDB::connect();

    // Was validating by ip, not anymore
    // $res = &$db->query("SELECT id from poll_submissions WHERE `submission_ip` = '$ip' AND `poll_id` = $poll_id;");
    // if ($row = $res->fetchRow()) return false;
    
    $sql = "INSERT INTO poll_submissions (poll_id, poll_value_id, submission_ip, submission_date) VALUES ($poll_id, $value_id, '$ip', NOW());";
    $db->exec($sql);
    
    return true;
  }
  
  /**
   * Gets the currently active poll question merged with the values for the question
   * @return array(id, question, question_count, poll_values => array(value_1, value_2, ...))
   */
  function getActivePoll(){
    $this->find(array('conditions'=>'active = 1'));
    $this->fetch();
    $poll = $this->toArray();
    $poll_values = $this->pollValues($poll['id'], $poll['question_count']);
    return array_merge($poll, array('poll_values'=>$poll_values));
  }
  
  /**
   * Gets the specified poll question merged with the values for the question
   * @return array(id, question, question_count, poll_values => pollValues() )
   */
  function getPoll($poll_id){
    $this->find(array('conditions'=>"id = $poll_id"));
    $this->fetch();
    $poll = $this->toArray();
    $poll_values = $this->pollValues($poll['id'], $poll['question_count']);
    return array_merge($poll, array('poll_values'=>$poll_values));  
  }
  
  /**
   * Get the values for the specified poll along with the number of votes and percentage score per value
   * @return array(value_1 => array(title, votes, percent), value_2 => array(), ...)
   */
  function pollValues($id, $count){
    $db = &NDB::connect();
    $res =& $db->query("SELECT * FROM `poll_values` WHERE `poll_id` = $id LIMIT $count");
    $values = $res->fetchAll();
    
    $total_votes = 0;
    $opts = array();

    foreach ($values as $i => $vals) {
      $k = 'value_'.($i+1);
      $opts[$k] = array();
      $opts[$k]['title'] = $vals['value'];
      $opts[$k]['votes'] = $this->getVotes($vals['id']);
      $total_votes += $opts[$k]['votes'];
    }
    foreach ($opts as &$opt) {
      $opt['percent'] = ($total_votes > 0) ? round($opt['votes'] / $total_votes * 100) : 0;
    }

    return $opts;

  }
  
  /**
   * Gets the number of votes for the given value
   */
  function getVotes($value_id){
    $db = &NDB::connect();
    $res = &$db->query("SELECT id FROM poll_submissions WHERE poll_value_id = $value_id;");
    return $res->numRows();
  }
  
  /**
   * Find the id for a poll_value given the poll id and the string value
   */
  function getValueId($poll_id, $value){
    $db = &NDB::connect();

    $res = &$db->query("SELECT `id` FROM poll_values WHERE `poll_id` = $poll_id AND `value` = '$value';");
    if ($row = $res->fetchRow()) return $row['id'];
    
    return false;
  }
  
}
?>