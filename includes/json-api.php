<?php /*
**************************************************************************

File:         json-api.php
What it does: creates an api endpoint for movies cpt including custom fields
**************************************************************************/

// add action
add_action( 'init', 'moxie_press_endpoint' );

function moxie_press_endpoint() {
    add_rewrite_tag( '%moxie_press%', '([^&]+)' );
    add_rewrite_rule( 'movies.json/?', 'index.php?moxie_press=a', 'top' );
    // todo: fix it so that params can be added
//  add_rewrite_rule( 'movies.json/([^&]+)/?', 'index.php?moxie_press=$matches[1]', 'top' );
}


register_activation_hook( __FILE__, 'moxie_press_rewrite_activation' );
function moxie_press_rewrite_activation()
{
    flush_rewrite_rules();
}

function moxie_press_endpoint_data() {
 
    global $wp_query;
 
    $movie = $wp_query->get( 'moxie_press' );
 
    if ( ! $movie ) {
        return;
    }
 
    $movie_data = array();
 
    $args = array(
        'post_type'      => 'movie',
        'posts_per_page' => 100,
    );
    $movie_query = new WP_Query( $args );
    if ( $movie_query->have_posts() ) : while ( $movie_query->have_posts() ) : $movie_query->the_post();
      $id = get_the_ID();
      $movie_data[] = array( 'data' => array(
          'id' => $id,
          'title' => get_the_title(),
          'poster_url'  => get_post_meta($id, 'moxie_press_poster_url'),
          'rating'  => get_post_meta($id, 'moxie_press_rating'),
          'year'  => get_post_meta($id, 'moxie_press_year'),
          'short_description'  => get_post_meta($id, 'moxie_press_description'),
      ));
    endwhile; wp_reset_postdata(); endif;
 
    wp_send_json( $movie_data );
 
}
add_action( 'template_redirect', 'moxie_press_endpoint_data' );




?>