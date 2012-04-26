<?php
require_once 'app/controllers/asset_controller.php';
require_once('lib/class.yahoostock.php');
class StockquoteController extends AssetController {
	function __construct() {
		$this->name = 'stockquote';
		$this->versioning = true;
		$this->base_view_dir = ROOT_DIR;
		$this->stock_format = "snl1d1t1cv"; /* Format below. */
		$this->stock_symbol = "glc.v";
		parent::__construct();
	}
	
	/*  Add format/parameters to be fetched
      s = Symbol
      n = Name
      l1 = Last Trade (Price Only)
      d1 = Last Trade Date
      t1 = Last Trade Time
      c = Change and Percent Change
      v = Volume */
	
	function stockquote() {
	  $html = '';
    $stockquote = new YahooStock;
    $stockquote->addFormat($this->stock_format);
    $stockquote->addStock($this->stock_symbol);
    foreach( $stockquote->getQuotes() as $code => $stock) {
        $this->set('stock_symbol', $stock[0]);
        $this->set('stock_name', $stock[1]);
        $this->set('last_trade', $stock[2]);
        $this->set('last_date', $stock[3]);
        $this->set('last_time', $stock[4]);
        $this->set('stock_change', $stock[5]);
        $this->set('stock_volume', $stock[6]);
        $html .= $this->render(array('action' => 'full', 'return' => true));
    }
    echo $html;
    
	}
	
	function sidebar() {
	  $html = '';
	  $stockquote = new YahooStock;
    $stockquote->addFormat($this->stock_format);
    $stockquote->addStock($this->stock_symbol);
    foreach( $stockquote->getQuotes() as $code => $stock) {
      $this->set('stock_symbol', $stock[0]);
      $this->set('stock_name', $stock[1]);
      $this->set('last_trade', $stock[2]);
      $this->set('last_date', $stock[3]);
      $this->set('last_time', $stock[4]);
      $this->set('stock_change', $stock[5]);
      $this->set('stock_volume', $stock[6]);
  		$html .= $this->render(array('action' => 'sidebar', 'return' => true));
    }
    echo $html;
	}
}
?>
