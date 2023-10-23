const mix = require('laravel-mix');


// set public path
mix.setPublicPath('dist')

// autoload jquery
mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
});

// compile js
mix.js('src/frontend/flight-management.js', 'js').sourceMaps( false ).extract( [ 'vue' ] ).vue();