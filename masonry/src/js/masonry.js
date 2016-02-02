/**************************************************************************

File:         masonry/src/masonry.js
What it does: consumes the service and renders movies on the page
**************************************************************************/

$(document).ready(function(){
  // some vars we will be using
  var endpoint = moxie_press_vars.endpoint;
  var myObj;
  var grid, myScreen = [];
  var filterContext = {};

  // initialize: get posts and displays them
  (function initialize(){

    // initialize container object
    filterContext = {
      sort_rating_order:  'desc',
      sort_year_order:    'desc',
      sort_year_text:     'year: ',
      sort_rating_text:   'rating: '
    };
    // render container template
    (function renderContainerTemplate(){
      var template = Handlebars.compile($("#moxie-template-container").html());
      var newHtml = template(filterContext);
      $('.moxie_press_container').append(newHtml);
    })();

    // get movie data and display Movie collection
    getPosts('',displayMovieCollection);

    // listen to buttons
    $('#mxp_sort_rating').click(function(e){
      e.preventDefault();
      sortPosts('rating',filterContext.sort_rating_order,displayMovieCollection);
      filterContext.sort_rating_order = ( filterContext.sort_rating_order == 'desc'? 'asc' : 'desc');
      $(this).find('span').html(filterContext.sort_rating_order);
      updateControls($(this));
      return false;
    });

    $('#mxp_sort_year').click(function(e){
      e.preventDefault();
      sortPosts('year',filterContext.sort_year_order,displayMovieCollection);
      filterContext.sort_year_order = ( filterContext.sort_year_order == 'desc'? 'asc' : 'desc');
      $(this).find('span').html(filterContext.sort_year_order);
      updateControls($(this));
      return false;
    });

    // remove other active buttons
    function updateControls($this){
      $('.mxp_filter').removeClass('active');
      $this.addClass('active');
    }
  })();

  function getPosts(query, callback){
    // query will be added later, if available
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

  // display movie collection using provided or filtered data
  function displayMovieCollection(data){
    //render template using movie collection from data
    var template = Handlebars.compile($("#moxie-template-movie").html());
    var newHtml = template(data);

    // animate the data change, keeping previous element height to avoid scroll jump
    $('.moxie-control').fadeIn(50);
    $('#moxie-grid').css('min-height',$('#moxie-grid').height());
    $('.mxp_grid-overlay').show().fadeOut();
    $('#moxie-grid').html(template(data));
    $('#moxie-grid').prepend('<div class="mxp_grid-sizer"></div>');
    updateGrid(); // activate masonry when finished
  }

  // update width attr for all poster images depending on the screen size
  function updateImageSize(){
    $('.mxp_poster img').each(function(index, item){
      var parentWidth;
      if(myScreen.width > 475) parentWidth = 150;
      else parentWidth = $(item).parents('.mxp_poster').width();
//      $(item).attr('width',parentWidth + 10);
    });      
  }

  // 
  function updateGrid(){
    // layout items again and define click handler
    if (grid !== undefined) {
      grid.masonry('destroy');
    }

    updateImageSize();

    // create masonry object
    grid = $('.mxp_grid').masonry({
      itemSelector: '.mxp_grid-item',
      columnWidth: '.mxp_grid-sizer',
      percentPosition: true
    });

    transformRating();

    // if trailer button is clicked then display and call youtube api
    $('.mxp_card-trigger-trailer').click(function(e){
      e.preventDefault();
      var item = $(this).parents('.mxp_grid-item');
      item.toggleClass('mxp_card-active');
      if(item.hasClass('mxp_card-active')) {
        insertVideo(item.find('.mxp_card-embed'),item.data('mdbid'));
        playThisVideo(item); // make this video auto play
      } else {
        pauseAllVideos(); // pause all the other videos in the page
      }
      grid.masonry('layout'); // lay the items out again
    })

    // click handler for each card
    grid.on( 'click', '.mxp_grid-item', function() {
      var item = $(this);
      if(!item.hasClass('mxp_grid-item--gigante')){ // if clicked card is already active

        // play or pause videos if card is open
        pauseAllVideos();
        if (item.hasClass('.mxp_card-active')) playThisVideo(item);

        // add/remove classes and recreate grid layout
        $('.mxp_grid-item').removeClass('mxp_grid-item--gigante').removeClass('mxp_card-active');
        $( this ).addClass('mxp_grid-item--gigante');
        grid.masonry('layout');

        // scroll to position, delay to allow grid to finish
        setTimeout(scrollToTarget.bind(null, item), 250);
      }
    }); 
  }

  // transform rating number into stars
  function transformRating(){
    $('.mxp_rating-transform').each(function(index, item){
      var rating = $(item).html().substr(1,1);
      var html = '';
      for(var i = 1; i <= 5; i++){
        if (i <= rating) html += '&#9733;';
      }
      $(item).html(html);
    });      
  }

  // scroll to target if grid-item position has changed
  function scrollToTarget(target){
    if(target.hasClass('mxp_grid-item--gigante')){
      $('html, body').animate({
          scrollTop: target.offset().top - 50
      }, 500);         
    }
  }

  // video functions: play and pause selected videos
  function playThisVideo(player){
    var player = $(player).find('.mxp_moxie-player');
    if(player !== undefined && player.length > 0) {
      player[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');   
    }

  }

  function pauseAllVideos(){
    // pause all youtube videos
    var players = $('.mxp_moxie-player');
    if(players !== undefined && players.length > 0) {
      players.each(function(index,player){
        player.contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');   
      });
    }
  }

  // call mdb api to get related videos, then call youtube to get the player
  function insertVideo(target,mdbid){
    var mdb_api = '4b94e36814dcea14914304d5f814330c';
    var url = 'https://api.themoviedb.org/3/movie/' + mdbid + '/videos?api_key=' + mdb_api;

    // exit (and autoplay) if player already exists on the target
    var player = target.find('.mxp_moxie-player');
    if(player != undefined && player.length > 0 ) {
      playThisVideo(player);
      return
    }

    // notify user and then perform ajax call
    if ( mdbid == undefined || mdbid.length == 0 ) {
      target.html('<p>Sorry, there are no related videos</p>');
      return
    }
    target.html('<p>Searching related videos or trailers in themoviedb.org</p>');

    // perform ajax call using mdb id
    $.ajax({
      url: url,
      cache: true,
      dataType: 'json',
      success: function(videos){
        var embed = "<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='moxie-embed-container' id='player-" + mdbid + "'><iframe class='mxp_moxie-player' id='moxie-player-"+mdbid+"' src='http://www.youtube.com/embed/" + videos.results[0].key + "?enablejsapi=1&version=3&playerapiid=ytplayer&rel=0&amp;autoplay=1' frameborder='0' width='100%' height='230' allowfullscreen='true' allowscriptaccess='always'></iframe></div>";
        if(videos.results !== undefined && videos.results.length > 0) target.html(embed);
        else target.html('<p>Sorry, we found no videos for this movie</p>');
      },
      error: function(err){
        console.log(err);
      }
    });
  }

  // filter according to field and sorting order
  function sortPosts(field, order, callback){
    var filtered = [];
    filtered.data = myObj.data.sort(function(a, b){
      return (order == 'asc' ? b[field] - a[field] : a[field] - b[field] );
    })
    callback(filtered);
  }

});
