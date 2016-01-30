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
    // get movie data and display Movie collection
    getPosts('',displayMovieCollection);
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
    var source   = $("#moxie-press").html();
    var template = Handlebars.compile(source);

    var html    = template(data);

    $('.moxie_press_container').html(html).fadeIn();
    updateGrid(updateImage); // activate masonry when finished

  }

  function updateGrid(callback){
    grid = $('.grid').masonry({
      // options
      itemSelector: '.grid-item',
      columnWidth: 180
    });
    return callback();
  }


  // update the background-image preoperty for every item in the grid
  function updateImage(){
    $('.grid-item').each(function(index, element){
      if($(element).data("img").length !== 0) $(element).css('background-image','url(' + $(element).data("img") + ')');
    });

    // enable clicking a card to make it giant
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
    var filtered = [];
    filtered.data = myObj.data.filter(function(movie){
      return movie.rating > 2;
    });
    return callback(myObj);
  }






});

