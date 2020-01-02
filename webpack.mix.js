let mix = require('laravel-mix');

// Compile css
mix.sass('resources/user/sass/app.scss', 'public/vendor/laradmin/user/css/user.css');
  
// Make css copy for publishing  by the package user through the service provider.
mix.copy('public/vendor/laradmin/user/','packages/bethelchika/laradmin/publishable/assets/user/');

// Copy raw assets to allow Laradmin package user to be able to change styles etc and recompile their own.
mix.copyDirectory('resources/user/','packages/bethelchika/laradmin/resources/user/') ;


//********************************************************************************/
  
mix.browserSync({proxy:'webferendum.com:8002'});

//and vendor. NOTE THAT LARADMIN IS NOT USING THESE JAVASCRIPT IN THE USER AREA.
//    mix.js('resources/js/app.js', 'public/js')
//    .extract(['lodash','jquery','bootstrap-sass','vue','axios'],'public/js/vendor.js')//Extract some tools into vendor folder
//    .autoload({//Avoid e.g library dependencies loading order issues
//     jquery: ['$',  'jQuery', 'jquery'],
// }); 

   
