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

const webpack = require('webpack')
const BundleAnalyzerplugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin


mix
   .disableNotifications()
   .js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .webpackConfig({
       plugins: [
           new BundleAnalyzerplugin({
               openAnalyzer: false
           }),
           new webpack.ContextReplacementPlugin(/moment[\/\\]local$/, /fr/),

       ]
   })
