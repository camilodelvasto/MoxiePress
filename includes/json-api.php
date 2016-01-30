<?php /*
**************************************************************************

File:         includes/json-api.php
What it does: creates an api endpoint for custom field "Movies" under /movies.json && /movies.json/movie-slug
**************************************************************************/

// add action
add_action( 'init', 'moxie_press_endpoint' );

function moxie_press_endpoint() {
    add_rewrite_tag( '%json%', '([^&]+)' );
    // rewrite rule to allow single movies to be retrieved using /movies.json/movie-slug
    add_rewrite_rule( 'movies.json/([^&]+)', 'index.php?json=true&post_type=movie&name=$matches[1]', 'top' );
    // rewrite rule to allow movie archive to be retrieved using /movies.json
    add_rewrite_rule( 'movies.json', 'index.php?post_type=movie&json=true', 'top' );
    // todo: consider more params
}


register_activation_hook( __FILE__, 'moxie_press_rewrite_activation' );

function moxie_press_rewrite_activation()
{
    flush_rewrite_rules();
}

function moxie_press_endpoint_data() {
 
    global $wp_query;

    // get query vars 
    $json = $wp_query->get( 'json' );
    $name = $wp_query->get( 'name' );

    // use this template redirect only if json is requested 
    if ( $json  != 'true' ) {
        return;
    }

    $movie_data = array();
 
    // default args
    $args = array(
        'post_type'      => 'movie',
        'posts_per_page' => 100,
    );
    if($name != '') $args['name'] = $name;

    // perform the query
    $movie_query = new WP_Query( $args );
    if ( $movie_query->have_posts() ) : while ( $movie_query->have_posts() ) : $movie_query->the_post();
      $id = get_the_ID();
      $movie_data[] = array(
          'id' => $id,
          'title' => get_the_title(),
          'poster_url'  => get_post_meta($id, 'moxie_press_poster_url',true),
          'rating'  => get_post_meta($id, 'moxie_press_rating',true),
          'year'  => get_post_meta($id, 'moxie_press_year',true),
          'short_description'  => get_post_meta($id, 'moxie_press_description',true),
          'mdbid'  => get_post_meta($id, 'moxie_press_mdbid',true),
      );
    endwhile; wp_reset_postdata(); endif;
 
    // send json data using built-in WP function
    wp_send_json( array('data' => $movie_data) );
 
}

// add the template redirect. notice the custom fields here are plugin-related, so it has to be tweaked for other post types
add_action( 'template_redirect', 'moxie_press_endpoint_data' );

?>