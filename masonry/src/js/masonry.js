/**************************************************************************

File:         masonry/src/masonry.js
What it does: consumes the service and renders movies on the page
**************************************************************************/

var myObj,initialized = false;
var grid, myScreen = [];

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
    $('#mxp_filter').click(function(e){
      e.preventDefault();
      filterPosts('',displayMovieCollection);
      return false;
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
    var newHtml = template(data);

    $('.moxie-control').fadeIn(50);
    $('#moxie-grid').css('min-height',$('#moxie-grid').height());
    $('.mxp_grid-overlay').show().fadeOut();
    $('#moxie-grid').html(template(data));
    updateGrid(); // activate masonry when finished

  }

  // set global grid width based on screen size
  (function getScreenSize(){
    // call on init and whenever the screen is resized
    setGridItemSize();
    $(window).resize(function(){
      setGridItemSize();
    });

    function setGridItemSize(){
      myScreen.height = $(window).height();
      myScreen.width = $(window).width();
      if(myScreen.width > 475) {
        myScreen.gridItem = 150;
      } else {
        myScreen.gridItem = (myScreen.width > 320? myScreen.width*45/100 : 160);
      }
      updateGrid();
    }
  })();

  // update width attr for all poster images depending on the screen size
  function updateImageSize(){
    $('.mxp_poster img').each(function(index, item){
      var parentWidth;
      if(myScreen.width > 475) parentWidth = 150;
      else parentWidth = $(item).parents('.mxp_poster').width();
      $(item).attr('width',parentWidth + 10);
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
      columnWidth: myScreen.gridItem
    });

    transformRating();

    // if trailer button is clicked then display and call youtube api
    $('.mxp_card-trigger-trailer').click(function(e){
      e.preventDefault();
      var item = $(this).parents('.mxp_grid-item');
      item.toggleClass('mxp_card-active');
      if(item.hasClass('mxp_card-active')) {
        insertVideo(item.find('.mxp_card-embed'),item.data('mdbid'));
        playThisVideo(item);
      } else {
        pauseAllVideos();
      }
      grid.masonry('layout'); // lay out items again
    })

    // click handler for each card
    grid.on( 'click', '.mxp_grid-item', function() {
      var item = $(this);
      if(!item.hasClass('mxp_grid-item--gigante')){ // if clicked card is already active

        pauseAllVideos();
        if (item.hasClass('.mxp_card-active')) playThisVideo(item);

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

  // scroll to target if grid has changed
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
    console.log(player.length);
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

  // filter functions
  function filterPosts(query, callback){
    // filter according to query, for now just fixed criteria: movie.rating > 2
    var filtered = [];
    filtered.data = myObj.data.filter(function(movie){
      return movie.rating > 2;
    });
    callback(filtered);
  }



});
