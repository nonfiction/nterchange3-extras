<?php
class PollHelper{
  
  function function_textstyle($params) {
    $percent = (int)round($params['for'] * 0.05);
    switch($percent) {
      case 0: return 'min';
      case 1: return 'twenty';
      case 2: return 'forty';
      case 3: return 'sixty';
      case 4: return 'eighty';
      case 5: return 'max';
    }
  }
}
?>