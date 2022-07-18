let mix = require('laravel-mix');
require('./nova.mix');

mix.setPublicPath('dist')
    .js('resources/js/tool.js', 'js')
    .js('resources/js/card.js', 'js')
    .vue({ version: 3 })
    .sass('resources/sass/tool.scss', '/css/tool.css')
    .nova('nova-google-analytics');
