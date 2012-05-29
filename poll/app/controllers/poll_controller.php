<?php
require_once 'app/controllers/asset_controller.php';
require_once 'n_db.php';

class PollController extends AssetController {
  function __construct() {
    $this->name = 'poll';
    $this->versioning = true;
    $this->base_view_dir = ROOT_DIR;
    $this->order_by = "active DESC, id ASC";
    $this->login_required = array('create', 'edit', 'values', 'show', 'activate');
    $this->auto_render = false;
    parent::__construct();
  }
  
  function bug(){
    $model = &$this->getDefaultModel();
    print "<pre>";
    print_r($model->getActivePoll());
  }
  
  /**
   * Frontend results view, presented by poll_controller.js
   */
  function results(){
    $model = &$this->getDefaultModel();
    $this->set($model->getActivePoll());
    $this->render('results');
  }

  /**
   * Frontend poll view, presented by poll_controller.js
   */
  function poll(){
    $model = &$this->getDefaultModel();
    $poll = $model->getActivePoll();
    $this->set($poll);
    
    if (array_key_exists('values', $_POST)){
      $poll_id = (int)$poll['id'];
      $submit_value = $poll['poll_values'][$_POST['values']]['title'];
      $value_id = $model->getValueId($poll_id, $submit_value);
      $ip = $_SERVER['REMOTE_ADDR'];

      $cookiename = 'polls_'.$poll_id;
      if (array_key_exists($cookiename, $_COOKIE)) {
        $this->set('flash', 'Sorry, only one vote per poll allowed');
      } else {
        setcookie($cookiename, 'voted');
        $model->submitVote($ip, $value_id, $poll_id);
        $this->set('flash', 'Thank you for voting!');
      }

      $this->results();

    } else {
      $this->render('poll');
    }
  }
  /**
   * Backend show view
   */ 
  function show($poll_id){
    $id = (int)$poll_id;
    $model = &$this->getDefaultModel();
    $this->set($model->getPoll($id));
    $this->render(array('layout'=>'simpleton'));
  }
  
  /**
   * Backend activate view - if the poll is activated, set activate to true
   * and redirect to viewlist
   */ 
  function activate($id){
    $id = (int)$id;

    $model = &$this->getDefaultModel();
    $this->set($model->getPoll($id));

    $activate = array_key_exists('activate', $_POST) ? true: false;
    if ($activate) {
      $model->activate($id);
      $this->redirectTo('viewlist');
    }
    $this->render(array('layout'=>'simpleton'));
  }

  /**
   * Backend create view - Display the form or create a poll question and redirect
   * to values
   */ 
  function create() {
    list($question, $question_count) = $this->pollQuestion();
    if ($question && $question_count) {
      $model = &$this->getDefaultModel();
      $model->cms_headline = $question;
      $model->question_count = $question_count;
      $model->active = false;
      $id = $model->insert();
      $this->redirectTo('values', $id);
    }
    $this->set('questions', range(1, 8));
    $this->render(array('layout'=>'simpleton'));
  }
  
  /**
   * Backend edit view - Display the form or edit the poll question and redirect
   * to values
   * @param $id int - The id for the poll question
   */ 
  function edit($id){
    $id = (int)$id;
    $model = &$this->getDefaultModel();
    
    list($question, $question_count) = $this->pollQuestion();

    if ($question && $question_count) {
      $model->id = $id;
      $model->cms_headline = $question;
      $model->question_count = $question_count;
      $model->update();
      $this->redirectTo('values', $id);
    }
    
    $model->find($id);
    $model->fetch();
    $this->set($model->toArray());
    $this->set('questions', range(1, 8));
    $this->render(array('layout'=>'simpleton'));
  }
  

  /**
   * Validate the post of a new question
   * @param   $_POST['question']
   * @param   $_POST['question_count']
   * @return  array(string|null, string|null);
   */
  private function pollQuestion(){
    $question = array_key_exists('question', $_POST) ? $_POST['question']: null;
    $question_count = array_key_exists('question_count', $_POST) ? $_POST['question_count']: null;
    return array($question, $question_count);
  }


  /**
   *  Set the values for a poll question in the poll_values table
   *  @param $id int  - The poll id
   *  @result Display the values form or process values and redirect to activate()
   */
  function values($id) {
    $model = &$this->getDefaultModel();

    $id = (int)$id;
    
    // Find the poll which we are setting values for
    $model->find((int)$id);
    $model->fetch();

    $this->set('poll_id', $model->id);
    $this->set('question', $model->cms_headline);
    $count = (int)$model->question_count;
    
    // Fill the poll_values array with whatever data we have
    $poll_values = $this->pollValues($id, $count);
    $poll_id = array_key_exists('poll_id', $_POST) ? $_POST['poll_id'] : null;

    // Updating values if poll_id was included in POST
    if ($poll_id){
      $model->dropUpdateValues($id, $poll_values);
      $this->redirectTo('activate', $id);
    }
    
    $this->set('values', $poll_values);
    $this->render(array('layout'=>'simpleton'));
  }


  /**
   * Helper to get the values for this poll either from $_POST, the DB or empty strings
   * @param $id int             - the poll id
   * @param $question_count int - the expected number of questions for this poll
   * @return array              - an array of poll values
   */
  private function pollValues($id, $question_count){
    $model = $this->getDefaultModel();
    $post_opts = array();
    $default_opts = array();
    foreach (range(1, $question_count) as $i) {
      $k = 'value_'.$i;
      if ( array_key_exists($k, $_POST) ) $post_opts[$k] = $_POST[$k];
      $default_opts[$k] = '';
    }
    $model_opts = $model->pollValues($id, $question_count);
    return array_merge($default_opts, $model_opts, $post_opts);
  }
}
?>
