/**
 * :external selector
 * example -  $('a:external').addClass('external')
 * matches any link pointing to a different host
 */
$.expr[':'].external = function(obj){
  return (obj.href != '') && !obj.href.match(/^mailto\:/) && (obj.hostname != location.hostname);
};

/**
 * :current selector
 * Matches if the link points to the current page
 * Ignores leading slash, .html and trailing slash in current & target pages
 */
$.expr[':'].current = function(obj){
  var hrefmatch = /^\/|\.html$|(\/)$/g
  ,   target  = obj.pathname.replace(hrefmatch, '')
  ,   current = window.location.pathname.replace(hrefmatch, '')
  ,   page_id = '_'+document.pageid;
  return ( target == current || target == page_id );
};
$(document).ready(function(){ document.pageid = $('body').attr('id') });
