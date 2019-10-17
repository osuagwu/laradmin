<?php

namespace BethelChika\Laradmin\Http\Controllers\User;


use Illuminate\Http\Request;
use BethelChika\Laradmin\User;

use BethelChika\Laradmin\Laradmin;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Http\Controllers\User\Traits\Billing\StripeInvoice;
use BethelChika\Laradmin\Http\Controllers\User\Traits\Billing\StripePayment;
use BethelChika\Laradmin\Http\Controllers\User\Traits\Billing\StripeSubscription;

class BillingController extends Controller
{
    use StripeSubscription;
    use StripeInvoice;
    use StripePayment;

    private $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('re-auth:30');

        $this->laradmin=$laradmin;

        // Load menu items for user settings
        $laradmin->contentManager->loadMenu('user_settings');

        $this->laradmin->assetManager->registerMainNavScheme('primary');
        $this->laradmin->assetManager->setContainerType('fluid');

        
      
    }
    /**
     * SHow billing index
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        $user=$request->user();
        $this->authorize('update', $user);
        
        $pageTitle='Billing';
        return view('laradmin::user.billing.index',compact('pageTitle'));
    }

    

    
}