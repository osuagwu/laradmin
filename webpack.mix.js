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
/*
mix.js('resources/assets/js/app.js', 'public/js')
    .extract(['vue','bootstrap-sass','jquery','axios'],'public/js')
    .js('resources/assets/admin/js/admin.js', 'public/admin/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/admin/sass/admin.scss', 'public/admin/css');
   */

 //Admin specific, excluding vendor
 //  mix.js('resources/assets/laradmin/admin/js/admin.js', 'public/admin/js')
 //   .sass('resources/assets/laradmin/admin/sass/admin.scss', 'public/admin/css');


//User specific
//     mix.js('resources/assets/laradmin/user/js/user.js', 'public/user/js')
//     .sass('resources/assets/laradmin/user/sass/user.scss', 'public/user/css');
     
//Site
     mix.sass('resources/sass/app.scss', 'public/css');
  
//and vendor.
   mix.js('resources/js/app.js', 'public/js')
   .extract(['lodash','jquery','bootstrap-sass','vue','axios'],'public/js/vendor.js')//Extract some tools into vendor folder
   .autoload({//Avoid e.g library dependencies loading order issues
    jquery: ['$',  'jQuery', 'jquery'],
}); 

   

// Make css copy for Laradmin user instead of doing a separate compilation
mix.copy('public/css/app.css','packages/bethelchika/laradmin/publishable/assets/user/css/user.css');
mix.copy('public/fonts/vendor/bootstrap-sass/bootstrap','packages/bethelchika/laradmin/publishable/assets/user/css/~bootstrap-sass/assets/fonts/bootstrap');


//********************************************************************************/
//    
//Site and vendor
  
mix.browserSync({proxy:'webferendum.com:8002'//,
                //files:['./resources/views/vendor/laradmin/user/index.blade.php']

});