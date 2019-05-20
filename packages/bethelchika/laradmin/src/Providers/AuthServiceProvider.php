<?php

namespace BethelChika\Laradmin\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
        'BethelChika\Laradmin\UserGroup'=>'BethelChika\Laradmin\Policies\UserGroupPolicy',
        'BethelChika\Laradmin\UserGroupMap'=>'BethelChika\Laradmin\Policies\UserGroupMapPolicy',
        'BethelChika\Laradmin\User'=>'BethelChika\Laradmin\Policies\UserPolicy',
        'BethelChika\Laradmin\UserMessage'=>'BethelChika\Laradmin\Policies\UserMessagePolicy',
        'BethelChika\Laradmin\WP\Models\Page'=>'BethelChika\Laradmin\WP\Policies\PagePolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Gate for control pannel access
        Gate::define('cp', 'BethelChika\Laradmin\Policies\ControlPanelPolicy@view');

        //  //Gate for users
        //  Gate::define('edit-page', 'BethelChika\Laradmin\Policies\UserGates@editPage');

        //  Gate::policy();
    } 
}
