var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var minifyCss = require('gulp-minify-css');

// watch and copy *.scss files to build destination folder
gulp.task('styles', function() {
  gulp.src('src/sass/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('build/css/'));

  // copy vendor styles
  gulp.src('src/vendor/css/**/*.css')
    .pipe(gulp.dest('build/vendor/css/'));
});

// minify and copy all JavaScript (except vendor scripts)
gulp.task('scripts', function() {
  gulp.src('src/js/**/*.js')
    .pipe(uglify())
    .pipe(gulp.dest('build/js'));

  // copy vendor files
  gulp.src('src/vendor/js/**')
    .pipe(gulp.dest('build/vendor/js'));
});

// default task
gulp.task('default',function() {
  gulp.run('scripts', 'styles');
  gulp.watch('src/sass/**/*.scss',['styles']);
});



