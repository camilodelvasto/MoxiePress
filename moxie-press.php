<?php /*

**************************************************************************

Plugin Name:  MoxiePress
Plugin URI:   https://github.com/camilodelvasto/MoxiePress
Description:  Create a CPT called movies with some custom fields, add an API endpoint generator and display them using a shortcode [moxie-press]
Version:      0.1
Author:       Camilo Delvasto
Author URI:   http://www.startics.com/
Text Domain:  moxie-press

**************************************************************************/


// initialize plugin
add_action( 'init', 'register_cpt_movie_collection' );

// register cpt
function register_cpt_movie_collection() {

  // set text-domain var
  $text_domain = 'moxie-press';

  // set default labels and args for this cpt
  $labels = array(
    'name'               => _x( 'Movies', 'post type general name', $text_domain ),
    'singular_name'      => _x( 'Movie', 'post type singular name', $text_domain ),
    'menu_name'          => _x( 'MoxieMovies', 'admin menu', $text_domain ),
    'name_admin_bar'     => _x( 'Movie', 'add new on admin bar', $text_domain ),
    'add_new'            => _x( 'Add New', 'Movie', $text_domain ),
    'add_new_item'       => __( 'Add New Movie', $text_domain ),
    'new_item'           => __( 'New Movie', $text_domain ),
    'edit_item'          => __( 'Edit Movie', $text_domain ),
    'view_item'          => __( 'View Movie', $text_domain ),
    'all_items'          => __( 'All Movies', $text_domain ),
    'search_items'       => __( 'Search Movies', $text_domain ),
    'parent_item_colon'  => __( 'Parent Movies:', $text_domain ),
    'not_found'          => __( 'No Movies found.', $text_domain ),
    'not_found_in_trash' => __( 'No Movies found in Trash.', $text_domain )
  );

  $args = array(
    'labels'             => $labels,
    'description'        => __( 'Description.', $text_domain ),
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'movie' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
  );

  register_post_type( 'movie_review', $args );
}

?>