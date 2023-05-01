const mix = require('laravel-mix');
const path = require('path');
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

mix.setPublicPath('src/assets')
    .js('src/resources/js/installer.js', 'js/installer.js').vue();

mix.webpackConfig({
    resolve: {
        alias: {
            "@core": path.resolve(__dirname, "src/resources/js/core/")
        }
    }
});
