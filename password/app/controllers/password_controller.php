<?php
require_once 'app/controllers/asset_controller.php';
require_once ROOT_DIR.'/app/models/password.php';

class PasswordController extends AssetController {
  function __construct() {
    $this->name = 'password';
    $this->versioning = true;
    $this->password = null;
    $this->base_view_dir = ROOT_DIR;
    parent::__construct();
  }

  /**
   * Add this action as a code caller on any page you want password protected
   *
   *   {call controller=password action=authenticate_for group=some_group}
   *
   */
  function authenticateFor($params){
    $group = array_key_exists('group', $params) ? $params['group'] : false;
    $this->password = Password::forGroup($group);
    if (!$this->passwordCookie()) {
      $this->redirect_url($_SERVER["REQUEST_URI"]);
      header("Location: ".$this->password->login_url);
      die();
    }
  }

  /**
   * Add this action as a code caller to the login page for the group
   *
   *   {call controller=password action=login group=some_group}
   *
   */
  function login($params){
    $group = array_key_exists('group', $params) ? $params['group'] : false;
    $this->password = Password::forGroup($group);

    if ($this->passwordCookie() || $this->passwordPosted()) {
      $this->redirect_url();
    } else {
      echo $this->render(array('action'=>'login', 'return'=> true));
    }
  }

  /**
   * Determines whether a valid password for this group is stored in cookies
   */
  private function passwordCookie() {
    $cookie_key = "group_password_{$this->password->cms_headline}";
    if (
      array_key_exists($this->password->path(), $_COOKIE) &&
      $this->password->match($_COOKIE[$this->password->path()])
    ) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Determines whether a valid password for this group has been POSTed
   */
  private function passwordPosted() {
    if (
      array_key_exists('password', $_POST) &&
      $this->password->match($_POST['password'])
    ) {
      setcookie($this->password->path(), $_POST['password'], 0, '/');
      return true;
    } else {
      return false;
    }
  }

  /**
   * Set or redirect to the given url
   */
  private function redirect_url($value=null){
    if ($value) {
      setcookie("redirect_url", $value, 0, '/');
    } else {
      $location = array_key_exists('redirect_url', $_COOKIE) ? $_COOKIE['redirect_url'] : '/';
      header("Location: ".$location);
      die();
    }
  }
}
?>
