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
        //TODO: See which of these policies can just be deleted so that Model and Table policies could be used in place of them. Just to many policies that each do not do much.
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

        // Gate for Admin control panel access
        Gate::define('cp', 'BethelChika\Laradmin\Policies\AdminPolicy@cp');//TODO: This 'cp' could be replaced with 'administer' below instead of having 'cp' and 'administer' b/b 'administer' is fine to be used to determine who can login to control panel.
        Gate::define('administer', 'BethelChika\Laradmin\Policies\AdminPolicy@administer');

        
        // General user check
        Gate::define('user.check', 'BethelChika\Laradmin\Policies\UserPolicy@userCheck');



        // Gates for model
        Gate::define('model.create', 'BethelChika\Laradmin\Policies\ModelPolicy@create');
        Gate::define('model.view', 'BethelChika\Laradmin\Policies\ModelPolicy@view');
        Gate::define('model.views', 'BethelChika\Laradmin\Policies\ModelPolicy@views');
        Gate::define('model.update', 'BethelChika\Laradmin\Policies\ModelPolicy@update');
        Gate::define('model.delete', 'BethelChika\Laradmin\Policies\ModelPolicy@delete');



        // Gates for table: 
        // TODO we could comment out these gates if there are no good usage. But I don't 
        // think they take much resource when not in use though.
        Gate::define('table.create', 'BethelChika\Laradmin\Policies\TablePolicy@create');
        Gate::define('table.view', 'BethelChika\Laradmin\Policies\TablePolicy@view');
        Gate::define('table.views', 'BethelChika\Laradmin\Policies\TablePolicy@views');
        Gate::define('table.update', 'BethelChika\Laradmin\Policies\TablePolicy@update');
        Gate::define('table.delete', 'BethelChika\Laradmin\Policies\TablePolicy@delete');

        //  Gate::policy();
    } 
}
