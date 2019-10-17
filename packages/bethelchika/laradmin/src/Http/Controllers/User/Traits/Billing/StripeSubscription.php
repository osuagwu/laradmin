<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits\Billing;

use BethelChika\Laradmin\Billing\Stripe\Stripe as StripeCache;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

trait StripeSubscription{
    

    /**
     * Show a customers subscriptions plans
    * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionIndex(Request $request, StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);

        $subscriptions=$user->subscriptions;

        $plans=[];
        $products=[];
        if(count($subscriptions)){
            $products=$stripe_cache->allProducts();
            $plans=$stripe_cache->allPlans();
        }
        

        $pageTitle='Subscriptions';
        return view('laradmin::user.billing.stripe.subscription.index',compact('pageTitle','subscriptions','products','plans'));
    }

    /**
     * Show form step 1 of a new subscription process
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionCreateStep1(Request $request,StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);

        if(!config('laradmin.billing.stripe.secret')){
            return redirect()->route('user-billing')->with('warning','The action cannot be completed. Please contact us.');
        }
 
        $products=$stripe_cache->allProducts();
        $plans=$stripe_cache->allPlans();
        //dd($plans);

        $pageTitle='New plan';
        return view('laradmin::user.billing.stripe.subscription.create_step1',compact('pageTitle','products','plans'));
    }

    /**
     * Show handle step 1 of a new subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionHandleStep1(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request, [
            'plan'=>'required|string',
            'quantity'=>'required|numeric',
        ]);

        $data['plan']=$request->plan;
        $data['quantity']=$request->quantity;
       
        // Persist data and we will clean up later
        $request->session()->put('billing.subscription.create', $data);

        return redirect()->route('user-billing-sub2');
    }
   
    /**
     * Show form for step2 of a new subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionCreateStep2(Request $request,StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);

        if (!$request->session()->has('billing.subscription.create')) {
            return redirect()->route('user-billing-sub');//Start all over
        }

        $data= $request->session()->get('billing.subscription.create', []);

        if (!count($data)) {
            return redirect()->route('user-billing-sub');//Start all over
        }

        // Get payment methods
        $payment_methods=collect();
        $default_payment_method=null;
        if ($user->hasStripeId()) {
            $payment_methods = $user->paymentMethods();

            $default_payment_method=$user->defaultPaymentMethod();
        }

        //
        $plan_id=$data['plan'];
        $quantity=$data['quantity'];   
        
        $plan=$stripe_cache->retrievePlan($plan_id);

        if(!$plan){
            return redirect()->route('user-billing-sub1')->with('warning','Unknown plan selected');
        }
        $product=$stripe_cache->retrieveProduct($plan->product);

        $intent= $user->createSetupIntent();

        $pageTitle='New plan';
        return view('laradmin::user.billing.stripe.subscription.create_step2',compact('pageTitle','intent','plan','product','quantity','payment_methods','default_payment_method','user'));
    }
       

    /**
     * Create a new subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionHandleStep2(Request $request,StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'payment_method'=>'required|string',
            'plan'=>'required|string',
            'quantity'=>'required|numeric',
        ]);


        $plan=$stripe_cache->retrievePlan($request->plan);
        $product=null;
        if($plan){
            $product=$stripe_cache->retrieveProduct($plan->product);
        }
        
        if(!$product){
            return back()->with('warning','Unknown product/plan was selected');
        }

        // Define the subscription name. This determines how you can find out which item a user
        // is subscribing to.// With this definition, we should not allow a user to actively 
        // subscribe to the same plan twice. So user should be able to have more than one 
        // active subscriptions to the same product as long as the plans are different.
        $subscription_name=$product->name.':'.$plan->nickname;
        
        // A new customer can reach here without actually being registered as a customer in 
        // Stripe. But before going any further the customer must be registered on Stripe.
        if(!$user->hasStripeId()){
            $user->createAsStripeCustomer();
        }

        

        // Handle subscription
        $trial_days=config('laradmin.billing.stripe.subscription.trial_days');
        try {
            $subscription_build=$user->newSubscription($subscription_name, $plan->id);
            if($trial_days){
                $subscription_build->trialDays($trial_days);
            }  
            $subscription_build->create($request->payment_method, ['quantity'=>$request->quantity]);//TODO: Check why the quantity here stopped working
        }
        catch (IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('user-billing-invoices')]
            );
        }

        // Clean up
        $request->session()->forget('billing.subscription.create');

        return redirect()->route('user-billing-subs')->with('success','Done');
        
    }

    /**
     * Create form for swapping subscription
     *
     * @param Request $request
     * @param string the name of subscription in Cashier which is actually product in Stripe.
     * @return \Illuminate\Http\Response
     */
    public function swapCreate(Request $request,$name,StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);

        $subscription=$user->subscription($name);
        if(!$subscription){
            return back()->with('warning','Unknown subscription');
        }

        $products=$stripe_cache->allProducts();
        $plans=$stripe_cache->allPlans();
            
        $pageTitle='Swap plan';
        return view('laradmin::user.billing.stripe.subscription.swap_create',compact('pageTitle','subscription','products','plans'));
        
    }

    /**
     * Swap subscription
     *
     * @param Request $request
     * @param string the name of subscription in Cashier which is actually product in Stripe.
     * @return \Illuminate\Http\Response
     */
    public function swap(Request $request,$name){
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'plan'=>'required|string',
        ]);

        // TODO: Check the new plan is not the same as the current plan.

        try {
            $user->subscription($name)->swapAndInvoice($request->plan);// TODO: Check if we should just swap and not border invoicing immediately
        } 
        catch (IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('user-billing-invoices')]
            );
        }
        return redirect()->route('user-billing-subs')->with('success','Subscription was successfully updated');
    }

    /**
     * Create form for updating quantity of subscription
     *
     * @param Request $request
     * @param string the name of subscription in Cashier which is actually product in Stripe.
     * @return \Illuminate\Http\Response
     */
    public function quantityCreate(Request $request,$name){
        $user=$request->user();
        $this->authorize('update', $user);

        $subscription=$user->subscription($name);
        if(!$subscription){
            return back()->with('warning','Unknown subscription');
        }
            
        $pageTitle='Update quantity of plan';
        return view('laradmin::user.billing.stripe.subscription.quantity_create',compact('pageTitle','subscription'));
        
    }

    /**
     * Update the quantity of subscription
     *
     * @param Request $request
     * @param string the name of subscription in Cashier which is actually product in Stripe.
     * @return \Illuminate\Http\Response
     */
    public function quantity(Request $request,$name){
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'quantity'=>'required|string',
        ]);

        
        $subscription=$user->subscription($name);

        if(!$subscription){
            return back()->with('warning','Unknown subscription');
        }

        $subscription->updateQuantity($request->quantity);

        return redirect()->route('user-billing-subs')->with('success','Subscription was successfully updated');
    }

     /**
     * Perform various actions on subscription
     *
     * @param Request $request
     * @param string the name of subscription in Cashier which is actually product in Stripe.
     * @param string $update_action The action to perform. e.g {'cancel','resume','sync-stripe-status'}. TODO: New action can be added into the method
     * @return \Illuminate\Http\Response
     */
    public function subscriptionUpdateAction(Request $request,$name,$update_action=null){
        $user=$request->user();
        $this->authorize('update', $user);

        $subscription=$user->subscription($name);

        if(!$subscription){
            return back()->with('warning','Unknown subscription');
        }

        $success_msg='Subscription was successfully updated';
        $actioned=true;
        switch(strtolower($update_action)){
            case 'sync-stripe-status'://Synch a subscription status with the corresponding record on Stripe
                $subscription->syncStripeStatus();
                $success_msg='Subscription status was synched';
                break;
            case 'cancel':
                $subscription->cancel();
                break;
            case 'resume':
                $subscription->resume();
                break;
            default:
                $actioned=false;
        }

        // Return quietly when action is not understood.
        if(!$actioned){
            return back();
        }


        return redirect()->route('user-billing-subs')->with('success',$success_msg);
    }


    /**
     * SHow plans
     *
     * @param Request $request
     * @param StripeCache $stripe_cache
     * @return \Illuminate\Http\Response
     */
    public function subscriptionPlans(Request $request,StripeCache $stripe_cache){
        $user=$request->user();
        $this->authorize('update', $user);
 
        $products=$stripe_cache->allProducts();
        $plans=$stripe_cache->allPlans();

        $this->laradmin->assetManager->registerMainNavScheme('default');
        $this->laradmin->assetManager->setContainerType('static');

        $pageTitle='Plans';
        
        return view('laradmin::user.billing.stripe.subscription.plans',compact('pageTitle','products','plans'));
        

        

    }

}

