<?php
require_once 'app/controllers/asset_controller.php';
class SearchController extends AssetController {
  function __construct() {
    $this->name = 'search';
    $this->versioning = true;
    $this->base_view_dir = ROOT_DIR;
    parent::__construct();
  }

  // {call controller=search action=search_form core=cnrl.com}
  function searchForm($params){

    // Core MUST be set in code caller!
    if (!isset($params['core'])){ return false; }
    $core = $params['core'];

    // Get query and start values
    $q = ($this->getParam('q')) ? $this->getParam('q') : '';
    $start = ($this->getParam('start') > 0) ? $this->getParam('start') : 0;

    // Print the search box
    $this->set('q', $q);
    print $this->render(array('action'=>'search_form', 'return'=>true));

    // If theres a query, set up the solr url
    if ($q){
      $url = "http://search.nonfiction.ca/{$core}/select?wt=phps&fl=*&hl=on&hl.fl=content&rows=10";
      $q = str_replace(' ', '+', $q);

      // Get the solr data in to a PHP object
      ob_start();
      include_once "{$url}&q={$q}&start={$start}";
      $data = unserialize(ob_get_contents());
      ob_end_clean();

      // Pull out the starting point, number of rows per page, and total number of results
      $start = intval($data['response']['start']);
      $rows = intval($data['responseHeader']['params']['rows']);
      $total = intval($data['response']['numFound']);

      $this->set('start', $start);
      $this->set('rows', $rows);
      $this->set('total', $total);

      // Get all the page links
      $pages = array();
      for ($page_start=0; $page_start<$total; $page_start=$page_start+$rows){
        $pages[] = "?q={$q}&start={$page_start}";
      }
      $this->set('pages', $pages);

      // Relative page links
      $this->set('current_page', "?q={$q}&start={$start}");
      $page_start = $start - $rows;
      $this->set('previous_page', ($page_start>=0) ? "?q={$q}&start={$page_start}" : -1);
      $page_start = $start + $rows;
      $this->set('next_page', ($page_start<$total) ? "?q={$q}&start={$page_start}" : -1);

      // Loop through the search results
      $items = array();
      if (isset($data['response']['docs'])){
        foreach($data['response']['docs'] as $doc){
          $item = array();
          $item['title'] = ($doc['title']) ? $doc['title'] : $doc['url'];
          $item['url'] = $doc['url'];
          $item['content'] = $data['highlighting'][$doc['url']]['content'][0];
          $items[] = $item;
        }
      }
      $this->set('items', $items);

      // Finally, render the results template
      return $this->render(array('action'=>'search_results', 'return'=>true));

    // Nothing searched
    } else {
      return "<p>&nbsp;<br><br><br></p>";
    }
  }

  // {call controller=search action=search_404 core=cnrl.com}
  function search404($params) {
    if ($this->getParam('q')) {
      return $this->searchForm($params);
    }
    $uri = NServer::env('PHP_SELF');
    $words = explode('/', $uri);
    // remove any empty elements
    foreach ($words as $i=>$val) {
      if(empty($val)){
        unset($words[$i]);
      }
    }
    $this->setParam('q', urldecode(implode(' ', $words)));
    return $this->searchForm($params);
  }

}
?>
