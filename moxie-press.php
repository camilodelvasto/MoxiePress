<?php /*

**************************************************************************

Plugin Name:  MoxiePress
Plugin URI:   https://github.com/camilodelvasto/MoxiePress
Description:  Create a CPT called movies with some custom fields,
              add an API endpoint generator and display them using a shortcode [moxie-press]
Version:      0.1
Author:       Camilo Delvasto
Author URI:   http://www.startics.com/
Text Domain:  moxie-press

**************************************************************************/


// initialize plugin
add_action( 'init', 'register_cpt_movie_collection' );
add_action( 'init', 'register_movie_custom_fields' );

// set text-domain var
define('TEXT_DOMAIN', 'moxie-press');

// register cpt with mostly default labels and args
function register_cpt_movie_collection() {

  // set default labels and args for this cpt
  $labels = array(
    'name'               => _x( 'Movies', 'movie', TEXT_DOMAIN ),
    'singular_name'      => _x( 'Movie', 'movie', TEXT_DOMAIN ),
    'menu_name'          => _x( 'MoxieMovies', 'movie', TEXT_DOMAIN ),
    'name_admin_bar'     => _x( 'Movie', 'movie', TEXT_DOMAIN ),
    'add_new'            => _x( 'Add New', 'movie', TEXT_DOMAIN ),
    'add_new_item'       => __( 'Add New Movie', TEXT_DOMAIN ),
    'new_item'           => __( 'New Movie', TEXT_DOMAIN ),
    'edit_item'          => __( 'Edit Movie', TEXT_DOMAIN ),
    'view_item'          => __( 'View Movie', TEXT_DOMAIN ),
    'all_items'          => __( 'All Movies', TEXT_DOMAIN ),
    'search_items'       => __( 'Search Movies', TEXT_DOMAIN ),
    'parent_item_colon'  => __( 'Parent Movies:', TEXT_DOMAIN ),
    'not_found'          => __( 'No Movies found.', TEXT_DOMAIN ),
    'not_found_in_trash' => __( 'No Movies found in Trash.', TEXT_DOMAIN )
  );

  $args = array(
    'labels'             => $labels,
    'description'        => __( 'Description.', TEXT_DOMAIN ),
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'movies' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title', 'thumbnail', 'custom-fields' )
  );

  // register this cpt & flush rewrite rules
  register_post_type( 'movie', $args );
  flush_rewrite_rules();

}

// register meta box for holding custom fields
function register_movie_custom_fields(){

  // add meta box
  add_action( 'add_meta_boxes', 'moxie_movies_add_meta_box' );

  function moxie_movies_get_meta( $value ) {
    global $post;

    $field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $field ) ) {
      return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
      return false;
    }
  }

  function moxie_movies_add_meta_box() {
    add_meta_box(
      'moxie_movies-moxie-movies',
      __( 'Moxie Movies', 'moxie_movies' ),
      'moxie_movies_html',
      'movie',
      'normal',
      'default'
    );
  }

  function moxie_movies_html( $post) {
    wp_nonce_field( '_moxie_movies_nonce', 'moxie_movies_nonce' ); ?>

    <p>
      <label for="moxie_movies_poster_url"><b><?php _e( 'Poster URL', TEXT_DOMAIN ); ?></b><br>
      <i>Paste here the URL of the poster image for this movie</i></label><br>
      <input type="text" name="moxie_movies_poster_url" id="moxie_movies_poster_url" value="<?php echo moxie_movies_get_meta( 'moxie_movies_poster_url' ); ?>">
    </p>

    <p>
      <label for="moxie_movies_rating"><b><?php _e( 'Rating', TEXT_DOMAIN ); ?></b><br>
      <i>Movie rating, from 1 to 5</i></label><br>
      <select name="moxie_movies_rating" id="moxie_movies_rating">
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_rating' ) === '1' ) ? 'selected' : '' ?>>1</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_rating' ) === '2' ) ? 'selected' : '' ?>>2</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_rating' ) === '3' ) ? 'selected' : '' ?>>3</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_rating' ) === '4' ) ? 'selected' : '' ?>>4</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_rating' ) === '5' ) ? 'selected' : '' ?>>5</option>
      </select>
    </p>

    <p>
      <label for="moxie_movies_year"><b><?php _e( 'Year', TEXT_DOMAIN ); ?></b></label><br>
      <i>The year the movie was published</i></label><br>
      <select name="moxie_movies_year" id="moxie_movies_year">
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_year' ) === '2016' ) ? 'selected' : '' ?>>2016</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_year' ) === '2015' ) ? 'selected' : '' ?>>2015</option>
        <option <?php echo (moxie_movies_get_meta( 'moxie_movies_year' ) === '1999' ) ? 'selected' : '' ?>>1999</option>
      </select>
    </p>

    <p>
      <label for="moxie_movies_description"><b><?php _e( 'Description', TEXT_DOMAIN ); ?></b><br>
      <i>Short description. Accepts HTML tags.</i></label><br>
      <textarea name="moxie_movies_description" id="moxie_movies_description" ><?php echo moxie_movies_get_meta( 'moxie_movies_description' ); ?></textarea>
    </p><?php
  }

  function moxie_movies_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['moxie_movies_nonce'] ) || ! wp_verify_nonce( $_POST['moxie_movies_nonce'], '_moxie_movies_nonce' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['moxie_movies_poster_url'] ) )
      update_post_meta( $post_id, 'moxie_movies_poster_url', esc_attr( $_POST['moxie_movies_poster_url'] ) );
    if ( isset( $_POST['moxie_movies_rating'] ) )
      update_post_meta( $post_id, 'moxie_movies_rating', esc_attr( $_POST['moxie_movies_rating'] ) );
    if ( isset( $_POST['moxie_movies_year'] ) )
      update_post_meta( $post_id, 'moxie_movies_year', esc_attr( $_POST['moxie_movies_year'] ) );
    if ( isset( $_POST['moxie_movies_description'] ) )
      update_post_meta( $post_id, 'moxie_movies_description', esc_attr( $_POST['moxie_movies_description'] ) );
  }
  add_action( 'save_post', 'moxie_movies_save' );



}






























?>