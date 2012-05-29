jQuery(document).ready(function($){
  $('head').append('<link rel="stylesheet" href="/stylesheets/poll.css" type="text/css" />');
  $container = $('.poll_container');
  
  var handlers = function(){
    $('#poll_submission_form').each(function(){
      $(this).submit(function(){
        var data = $(this).serialize();
        $.post('/nterchange/poll/poll', data, function(data){
          $container.html(data);
          handlers();
        })
        return false;
      });
    });

    $("#view_results").click(function(ev){
      ev.preventDefault();
      show_results_page();
      return false;
    });

    $("#view_poll").click(function(ev){
      ev.preventDefault();
      show_poll_page();
      return false;
    });
  };

  function show_poll_page(){
    $.get('/nterchange/poll/poll', function(data){
      $container.html(data);
      handlers();
    });
  };
  
  function show_results_page(){
    $.get('/nterchange/poll/results', function(data){
      $container.html(data);
      handlers();
    });
  };

  show_poll_page();
});