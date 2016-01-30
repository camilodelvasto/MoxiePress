/**************************************************************************

File:         masonry/src/masonry.js
What it does: consumes the service and renders movies on the page
**************************************************************************/

var myObj,initialized = false;
var grid;

$(document).ready(function(){
  var endpoint = moxie_press_vars.endpoint;

  // initialize: get posts and displays them
  (function initialize(){

    // render container template
    var template = Handlebars.compile($("#moxie-template-container").html());
    $('.moxie_press_container').append(template());

    // get movie data and display Movie collection
    getPosts('',displayMovieCollection);

    // listen to controls
    $('#filter').click(function(e){
      e.preventDefault();
      filterPosts('',displayMovieCollection);
    })

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
  function displayMovieCollection(data){
    //render template using movie collection from data
    var template = Handlebars.compile($("#moxie-template-movie").html());
    $('#moxie-grid').fadeOut(0).html(template(data)).fadeIn();

    updateGrid(); // activate masonry when finished

  }

  function updateGrid(){
    // layout items again and define click handler
    if (grid !== undefined) {
      grid.masonry('destroy');
      grid.unbind('click');
    }
    grid = $('.grid').masonry({
      itemSelector: '.grid-item',
      columnWidth: 160
    });

    // click handler
    grid.on( 'click', '.grid-item', function() {
      $('.grid').children('.grid-item').not(this).removeClass('grid-item--gigante');
      $( this ).toggleClass('grid-item--gigante');
      grid.masonry('layout');

      if($(this).hasClass('grid-item--gigante')){
        $this = $(this);
        $('html, body').animate({
            scrollTop: $this.offset().top
        }, 500);
      }
    });  

  }

  function filterPosts(query, callback){
    // filter according to query, for now just fixed criteria: movie.rating > 2
    var filtered = [];
    filtered.data = myObj.data.filter(function(movie){
      return movie.rating > 2;
    });
    callback(filtered);
  }

});

