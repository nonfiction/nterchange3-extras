<?php
require_once 'n_model.php';

class Youtube extends NModel {
  function __construct() {
    $this->__table = 'youtube';
    $this->form_required_fields[] = 'embed_html';
    $this->form_elements['thumbnail_image'] = 'cms_file';

    $align_classes = array('default'=>"Default", 'left'=>"Left", 'right'=>"Right", 'center'=>"Center");
    $this->form_elements['align'] = array('select', 'align', 'Align', $align_classes);

    parent::__construct();
  }
}
?>