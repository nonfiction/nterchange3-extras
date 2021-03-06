<?php
require_once 'app/controllers/asset_controller.php';
class YoutubeController extends AssetController {
  var $embed_width = 600;
  var $embed_height = 380;

  function __construct() {
    $this->name = 'youtube';
    $this->versioning = true;
    $this->base_view_dir = ROOT_DIR;
    parent::__construct();
  }
  
  function embedVideo(){
    // Assumes default output of Share > Embed ; eg:
    // <iframe width="560" height="315" src="http://www.youtube.com/embed/ae73CUBqSo4" frameborder="0" allowfullscreen></iframe>
    // width/height are modified and query string is appended to src

    $vid_id = (int)$this->getParam('vid_id');
    $model = $this->getDefaultModel();

    // Regex's to search for in the embed url
    $w_pattern = '/width=.(\d+)./i';
    $h_pattern = '/height=.(\d+)./i';
    $src_pattern = '/src="(.*)"/U';
    
    // Replacement strings
    $w_replace = "width=\"{$this->embed_width}\"";
    $h_replace = "height=\"{$this->embed_height}\"";
    $youtube_opts = "rel=0";
    $src_replace = 'src="${1}?'.$youtube_opts.'"';
    
    if($model->find($vid_id)) {
      $model->fetch();
      $embed = $model->embed_html;
      $embed = preg_replace($w_pattern, $w_replace, $embed);
      $embed = preg_replace($h_pattern, $h_replace, $embed);
      if (! stristr($embed, '?')) {
        // add the rel=0 if we don't have a query string
        $embed = preg_replace($src_pattern, $src_replace, $embed);
      }
      $this->set('embed', $embed);
      $this->render('lightwindow');
    }
  }
}
?>
