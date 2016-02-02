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
  $dir = dirname(__FILE__);
  @include_once "$dir/includes/json-api.php";
  @include_once "$dir/includes/shortcode.php";
  @include_once "$dir/masonry/masonry.php";

  add_action( 'init', 'moxie_press_register_cpt_movie_collection' );
  add_action( 'init', 'moxie_press_register_movie_custom_fields' );

  // register cpt with mostly default labels and args
  function moxie_press_register_cpt_movie_collection() {

    // set default labels and args for this cpt
    $labels = array(
      'name'               => _x( 'Movies', 'movie', 'moxie-press' ),
      'singular_name'      => _x( 'Movie', 'movie', 'moxie-press' ),
      'menu_name'          => _x( 'MoxieMovies', 'movie', 'moxie-press' ),
      'name_admin_bar'     => _x( 'Movie', 'movie', 'moxie-press' ),
      'add_new'            => _x( 'Add New', 'movie', 'moxie-press' ),
      'add_new_item'       => __( 'Add New Movie', 'moxie-press' ),
      'new_item'           => __( 'New Movie', 'moxie-press' ),
      'edit_item'          => __( 'Edit Movie', 'moxie-press' ),
      'view_item'          => __( 'View Movie', 'moxie-press' ),
      'all_items'          => __( 'All Movies', 'moxie-press' ),
      'search_items'       => __( 'Search Movies', 'moxie-press' ),
      'parent_item_colon'  => __( 'Parent Movies:', 'moxie-press' ),
      'not_found'          => __( 'No Movies found.', 'moxie-press' ),
      'not_found_in_trash' => __( 'No Movies found in Trash.', 'moxie-press' )
    );

    $args = array(
      'labels'             => $labels,
      'description'        => __( 'Description.', 'moxie-press' ),
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
  function moxie_press_register_movie_custom_fields(){

    // add meta box
    add_action( 'add_meta_boxes', 'moxie_press_add_meta_box' );

    function moxie_press_get_meta( $value ) {
      global $post;

      $field = get_post_meta( $post->ID, $value, true );
      if ( ! empty( $field ) ) {
        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
      } else {
        return false;
      }
    }

    function moxie_press_add_meta_box() {
      add_meta_box(
        'moxie_press-moxie-movies',
        __( 'Moxie Movies', 'moxie_press' ),
        'moxie_press_html',
        'movie',
        'normal',
        'default'
      );
    }

    // write the html for the editor
    function moxie_press_html( $post) {
      wp_nonce_field( '_moxie_press_nonce', 'moxie_press_nonce' ); ?>

      <p>
        <label for="moxie_press_poster_url"><b><?php _e( 'Poster URL', 'moxie-press' ); ?></b><br>
        <i>Paste here the URL of the poster image for this movie</i></label><br>
        <input type="text" name="moxie_press_poster_url" id="moxie_press_poster_url" value="<?php echo moxie_press_get_meta( 'moxie_press_poster_url' ); ?>">
      </p>

      <p>
        <label for="moxie_press_rating"><b><?php _e( 'Rating', 'moxie-press' ); ?></b><br>
        <i>Movie rating, from 1 to 5</i></label><br>
        <select name="moxie_press_rating" id="moxie_press_rating">
          <option <?php echo (moxie_press_get_meta( 'moxie_press_rating' ) === '1' ) ? 'selected' : '' ?>>1</option>
          <option <?php echo (moxie_press_get_meta( 'moxie_press_rating' ) === '2' ) ? 'selected' : '' ?>>2</option>
          <option <?php echo (moxie_press_get_meta( 'moxie_press_rating' ) === '3' ) ? 'selected' : '' ?>>3</option>
          <option <?php echo (moxie_press_get_meta( 'moxie_press_rating' ) === '4' ) ? 'selected' : '' ?>>4</option>
          <option <?php echo (moxie_press_get_meta( 'moxie_press_rating' ) === '5' ) ? 'selected' : '' ?>>5</option>
        </select>
      </p>

      <p>
        <label for="moxie_press_year"><b><?php _e( 'Year', 'moxie-press' ); ?></b></label><br>
        <i>The year the movie was published</i></label><br>
        <select name="moxie_press_year" id="moxie_press_year"> <?php
          for($year = 2016; $year > 1900; $year--){ ?>
            <option <?php echo (moxie_press_get_meta( 'moxie_press_year' ) === $year ) ? 'selected' : '' ?>><?php echo $year ?></option><?php;
          } ?>
        </select>
      </p>

      <p>
        <label for="moxie_press_description"><b><?php _e( 'Description', 'moxie-press' ); ?></b><br>
        <i>Short description, restricted to 200 chars</i></label><br>
        <textarea name="moxie_press_description" id="moxie_press_description" cols="50" rows="10"><?php echo moxie_press_get_meta( 'moxie_press_description' ); ?></textarea>
      </p>

      <p>
        <label for="moxie_press_mdbid"><b><?php _e( 'Movie Database ID', 'moxie-press' ); ?></b><br>
        <i>Paste here the ID of this movie on <a href="https://www.themoviedb.org" target="_blank" >www.themoviedb.org</a></i></label><br>
        <input type="text" name="moxie_press_mdbid" id="moxie_press_mdbid" value="<?php echo moxie_press_get_meta( 'moxie_press_mdbid' ); ?>">
      </p><?php
    }

    // save custom fields
    function moxie_press_save( $post_id ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
      if ( ! isset( $_POST['moxie_press_nonce'] ) || ! wp_verify_nonce( $_POST['moxie_press_nonce'], '_moxie_press_nonce' ) ) return;
      if ( ! current_user_can( 'edit_post', $post_id ) ) return;

      if ( isset( $_POST['moxie_press_poster_url'] ) )
        update_post_meta( $post_id, 'moxie_press_poster_url', esc_attr( $_POST['moxie_press_poster_url'] ) );
      if ( isset( $_POST['moxie_press_rating'] ) )
        update_post_meta( $post_id, 'moxie_press_rating', esc_attr( $_POST['moxie_press_rating'] ) );
      if ( isset( $_POST['moxie_press_year'] ) )
        update_post_meta( $post_id, 'moxie_press_year', esc_attr( $_POST['moxie_press_year'] ) );
      if ( isset( $_POST['moxie_press_description'] ) )
        update_post_meta( $post_id, 'moxie_press_description', esc_attr( substr($_POST['moxie_press_description'], 0, 200 ) ) );
      if ( isset( $_POST['moxie_press_mdbid'] ) )
        update_post_meta( $post_id, 'moxie_press_mdbid', esc_attr( $_POST['moxie_press_mdbid'] ) );
    }
    add_action( 'save_post', 'moxie_press_save' );

  }

?>