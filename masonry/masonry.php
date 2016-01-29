<?php /*
**************************************************************************

File:         masonry/masonry.php
What it does: creates a masonry layout for movies created by moxie-press
**************************************************************************/


  // register all scripts and styles used by the masonry theme
  add_action("wp_enqueue_scripts", "moxie_press_masonry_register_scripts_styles");

  function moxie_press_masonry_register_scripts_styles() {
    $dir = plugin_dir_url( __FILE__ );

    wp_deregister_script('jquery');
    wp_register_script('jquery', "//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", '',false, null);
    wp_register_script( 'masonry-vendor', $dir . 'build/vendor/js/masonry.pkgd.min.js', 'jquery'  );
    wp_register_script( 'masonry-theme', $dir . 'src/js/masonry.js', array('jquery', 'masonry-vendor'), '', false  );
    wp_register_style( 'masonry-style', $dir . 'build/css/style.css','','', 'screen' );
 
     //to do: include minized js files
  }

  // enqueue scripts and styles only if shortcode present
  function moxie_press_masonry_enqueue_scripts_styles(){
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'masonry-vendor' );
    wp_enqueue_script( 'masonry-theme' );
    wp_enqueue_style( 'masonry-style' );
  }

  // receive the endpoint and enqueue scripts
  function moxie_press_masonry_display_movies($endpoint){
    wp_localize_script( 'masonry-theme', 'moxie_press_vars', array('endpoint' => $endpoint)) ;
  }
