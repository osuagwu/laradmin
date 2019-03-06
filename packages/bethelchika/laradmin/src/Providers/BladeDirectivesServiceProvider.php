<?php

namespace BethelChika\Laradmin\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Menu TODO: The render method called here is actually not implemented fully yet
        Blade::directive('menu', function ($tag) {
            return "<?php echo \BethelChika\Laradmin\Menu\Menu::render($tag); ?>";
        });

        //
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}