<?php
require_once 'n_model.php';

class Password extends NModel {
  function __construct() {
    $this->__table = 'password';
    $this->form_elements['cms_headline'] = array('text', 'cms_headline', 'Group');
    $this->form_required_fields[] = 'cms_headline';
    $this->form_required_fields[] = 'login_url';
    $this->form_elements['hashed_password'] = array('hidden', 'hashed_password', 'Hashed Password');
    $this->_order_by = 'cms_headline';
    $this->search_field = 'cms_headline';
    $this->salt = '16f4462bb0a630f1e65b88155061ba53';
    parent::__construct();
  }

  function beforeUpdate(){
    if ($this->password) {
      $this->hashed_password = crypt($this->password, $this->salt);
      $this->password = '';
    }
  }

  function match($str){
    return (crypt($str, $this->salt) == $this->hashed_password);
  }

  function path(){
    return "group_password_{$this->cms_headline}";
  }

  public static function forGroup($group){
    if (!$group) throw new Exception("Group not specified", 1);

    $model = NModel::factory('password');
    $conditions = array('conditions'=>"cms_headline = '{$group}'");

    if ($model->find($conditions)) { $model->fetch(); }
    else { throw new Exception("Invalid password group: {$group}", 1); }

    return $model;
  }
}
