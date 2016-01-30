/**************************************************************************

File:         masonry/src/masonry.js
What it does: consumes the service and renders movies on the page
**************************************************************************/

var myObj,initialized = false;

$(document).ready(function(){
  var endpoint = moxie_press_vars.endpoint;

  // initialize: get posts and displays them
  (function initialize(){
    // get movie data and display Movie collection
    getPosts('',displayMovieCollection);
    createGrid();
  })();

  function getPosts(query, callback){
    // query will be added later, if available on the shortcode
    var url = endpoint + query;
    $.ajax({
      url: url,
      cache: true,
      dataType: 'json',
      success: function(json){
        myObj = json;
        return callback(json);
      },
      error: function(json){
        console.log('error:',data);
      }
    })
  }

  // display movie collection using provided (filtered) data and movie template
  function displayMovieCollection(json){

    var source   = $("#moxie-press").html();
    var template = Handlebars.compile(source);

    console.log(json);
    var context = json;
    var html    = template(context);

    $('.moxie_press_container').append(html).fadeIn();

  }



});

