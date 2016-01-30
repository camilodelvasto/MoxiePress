<?php /*
**************************************************************************

File:         includes/shortcode.php
What it does: creates a shortcode that will read from the api endpoint and will display the movies whenever is called
**************************************************************************/

  add_shortcode('moxie-press', 'moxie_press_shortcode');

  function moxie_press_shortcode(){
    // define the endpoint
    $endpoint = get_home_url() . '/movies.json';

    //choose theme: for now it defaults to masonry
    $theme = 'masonry';

    if($theme == 'masonry'){
      // enqueue scripts and styles only if shortcode present
      moxie_press_masonry_enqueue_scripts_styles();

      // render movies with given endpoint
      moxie_press_masonry_display_movies($endpoint);

      // render the container div for this instance
      moxie_press_masonry_render_template();
    }
  }

?>
