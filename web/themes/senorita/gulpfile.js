let gulp = require('gulp'),
  sourcemaps = require('gulp-sourcemaps'),
  cleanCss = require('gulp-clean-css'),
  rename = require('gulp-rename'),
  postcss = require('gulp-postcss'),
  autoprefixer = require('autoprefixer'),
  browserSync = require('browser-sync').create()
  const sass = require('gulp-sass')(require('sass'));

const paths = {
  scss: {
    src: './patterns/styles.scss',
    dest: './dist/css',
    watch: './patterns/**/*.scss',
  },
  js: {
    jquery: './node_modules/jquery/dist/jquery.min.js',
    popper: 'node_modules/popper.js/dist/umd/popper.min.js',
    popper: 'node_modules/popper.js/dist/umd/popper.min.js.map',
    dest: './dist/js'
  }
}

function styles () {
  return gulp.src([paths.scss.src])
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([autoprefixer({
      browsers: [
        'Chrome >= 35',
        'Firefox >= 38',
        'Edge >= 12',
        'Explorer >= 10',
        'iOS >= 8',
        'Safari >= 8',
        'Android 2.3',
        'Android >= 4',
        'Opera >= 12']
    })]))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.scss.dest))
    .pipe(cleanCss())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.scss.dest))
}

// Move the javascript files into our js folder
function js () {
  return gulp.src([paths.js.jquery, paths.js.popper])
    .pipe(gulp.dest(paths.js.dest))
}

// Static Server + watching scss/html files
function serve () {
  gulp.watch([paths.scss.watch], styles).on('change', browserSync.reload)
}

const build = gulp.series(styles, gulp.parallel(js, serve))

exports.styles = styles
exports.js = js
exports.serve = serve

exports.default = build
