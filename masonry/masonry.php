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
    wp_register_script('handlebars', "//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js", '',false, null);
    wp_register_style( 'masonry-style', $dir . 'build/css/style.css','','', 'screen' );
     //to do: include minized js files
  }

  // enqueue scripts and styles only if shortcode present
  function moxie_press_masonry_enqueue_scripts_styles(){
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'masonry-vendor' );
    wp_enqueue_script( 'masonry-theme' );
    wp_enqueue_script( 'handlebars' );
    wp_enqueue_style( 'masonry-style' );
  }

  // receive the endpoint and enqueue scripts
  function moxie_press_masonry_display_movies($endpoint){
    wp_localize_script( 'masonry-theme', 'moxie_press_vars', array('endpoint' => $endpoint)) ;
  }

  // render the handlebars precompiled templates
  function moxie_press_masonry_render_template(){
    $dir = dirname(__FILE__);
    echo '<div class="moxie_press_container">'; 
    echo '<script id="moxie-template-movie" type="text/x-handlebars-template">';
      @include_once "$dir/templates/movies.handlebars";
    echo '</script>'; 
    echo '<script id="moxie-template-container" type="text/x-handlebars-template">';
      @include_once "$dir/templates/container.handlebars";
    echo '</script>'; 
    echo '</div>';
  }

  // prevent user to zoom while using the plugin
  add_action('wp_head','hook_disable_zoom');

  function hook_disable_zoom() {
    $output="<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />";
    echo $output;
  }

  

