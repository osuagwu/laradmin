<?php

namespace BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','pre-authorise']);

        app('laradmin')->contentManager->loadAdminMenu();
    }

    /**
     * Runs the authorisation for CP
     *
     * @return void
     */
    public function cpAuthorize(){
        if (Gate::denies('cp')) {
           abort(403);
       }
    }
}
