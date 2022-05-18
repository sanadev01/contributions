const mix = require('laravel-mix');

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

mix
    // .js('resources/js/app.js', 'public/js')
    .js('resources/js/pages/barcode.js', 'public/js/pages/barcode/reader.js')
    .js('resources/js/pages/scanner.js', 'public/js/pages').version();
    // .sass('resources/sass/app.scss', 'public/css');
