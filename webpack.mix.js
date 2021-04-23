let mix = require('laravel-mix')

mix.js('resources/js/card.js', 'dist/js')
    .js('resources/js/tool.js', 'dist/js');
mix.styles('resources/sass/tool.scss', 'dist/css/tool.css');
