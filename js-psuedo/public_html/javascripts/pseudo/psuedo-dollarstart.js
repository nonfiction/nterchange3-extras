/**
 * :dollarstart selector
 * matches if the elements text content starts with a $ sign
 */
$.expr[':'].dollarstart = function(obj){
  return ($(obj).html().match(/^(\s)*\$/) != null);
};

// Example - find all td's that begin with $ and break the text
// into two spans
$(document).ready(function(){
  $('td:dollarstart').each(function(index, elem){
    var $elem      = $(elem)
    ,   text       = $elem.html().replace(/^[\s]*\$/, '')
    ,   textNode   = "<span>"+text+"</span"
    ,   dollarNode = "<span class='dollar'>$</span>"
    ;
    $elem.html(dollarNode+textNode);
  });
});