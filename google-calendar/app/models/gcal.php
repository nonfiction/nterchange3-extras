<?php
require_once 'n_model.php';

class Gcal extends NModel {
	function __construct() {
		$this->__table = 'gcal';
		$this->_order_by = 'cms_headline';
		parent::__construct();
	}
}
?>