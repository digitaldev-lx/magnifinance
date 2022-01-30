let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .scripts([
        'public/assets/js/jquery-3.6.0.min.js',
        'public/assets/js/popper.min.js',
        'public/assets/js/bootstrap.min.js',
        'public/front-assets/js/helper.js'
    ], 'public/assets/js/front-scripts.js')
    .styles([
        'public/assets/css/bootstrap.min.css',
        'public/assets/css/font-awesome.min.css'
    ], 'public/assets/css/front-styles.css')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .options({ processCssUrls: false })
    .sourceMaps(true, 'source-map');
