![](http://res.cloudinary.com/startics/image/upload/c_scale,w_629/v1454420140/Screen_Shot_2016-02-02_at_8.35.16_AM_js5anw.jpg)

#MoxiePress

A modern plugin for WordPress that will display your movie collection using a shortcode: `[moxie-press]`.

The plugin does the following:

1. Creates a custom post type with custom fields for title, poster image, year, rating, description and The Movie Database ID
2. Creates an API endpoint like `/movies.json` and `/moxies.json/movie-name`, caching the request and clearing the cache on post save
3. Displays the movie collection on any page via the shortcode `[moxie-press]`, showing movie data and a movie trailer if TMDb ID is provided and if there's a related video on the [www.themoviedb.org](www.themoviedb.org)

The plugin can be further enhanced by:

- Creating an options page and adding other themes
- Adding more parameters to the shortcode call
- Lazy loading of images
- Pagination and loading of pages on scroll
- Pre compilation of handlebar templates for production
- Clearing expired transients
- Making original image size adapt to card size
- Using backbone, Angular or another framework to separate views from the model and improve re usability

##For developers only

The plugin uses the following files:

- **moxie-press-php**: main file, custom post type and field definitions and calls to methods on the other files
- **json-api.php**: rewrite tags and redirection rules, wp query based on arguments, caching
- **shortcode.php**: template (handlebars) inclusion, assets inclusion of styles and scripts for the current theme, masonry
- **masonry folder**: sass, js files, vendor scripts (masonry) and handlebar templates

Gulpfile.js is included, but you can use grunt or whatever for preprocessing.

