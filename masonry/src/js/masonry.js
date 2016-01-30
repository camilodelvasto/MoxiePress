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
      if(!$(this).hasClass('grid-item--gigante')){
        $('.grid-item').removeClass('grid-item--gigante');
        $( this ).addClass('grid-item--gigante');
        $(this).find('.card-info').addClass('active');
        grid.masonry('layout');

        // scroll to position todo: fix!!!!
        if($(this).hasClass('grid-item--gigante')){
          $this = $(this);
          $('html, body').animate({
              scrollTop: $this.offset().top - 50
          }, 500);          
        }
      } else {
        // card is already active
        if($(this).find('.card-info').hasClass('active')){
          $(this).find('.card-info').removeClass('active').fadeOut(50);
          $(this).find('.card-embed').fadeIn();
          insertVideo($(this).find('.card-embed'),$(this).data('mdbid'));
        } else {
          $(this).find('.card-info').addClass('active').fadeIn();
          $(this).find('.card-embed').fadeOut(50);
        }
      }
    });  

  }

  function insertVideo(target,mdbid){
    var mdb_api = '4b94e36814dcea14914304d5f814330c';
    var url = 'https://api.themoviedb.org/3/movie/' + mdbid + '/videos?api_key=' + mdb_api;

    // notify user and then perform ajax call
    if ( mdbid == undefined || mdbid.length == 0 ) {
      target.html('<p>Sorry, there are no related videos</p>');
      return
    }
    target.html('<p>Searching related videos or trailers in themoviedb.org</p>');
    $.ajax({
      url: url,
      cache: true,
      dataType: 'json',
      success: function(videos){
        var embed = "<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='http://www.youtube.com/embed/" + videos.results[0].key + "' frameborder='0' allowfullscreen></iframe></div>";
        if(videos.results !== undefined && videos.results.length > 0) target.animate().html(embed);
        else target.html('<p>Sorry, we found no videos for this movie</p>');
      },
      error: function(err){
        console.log(err);
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

