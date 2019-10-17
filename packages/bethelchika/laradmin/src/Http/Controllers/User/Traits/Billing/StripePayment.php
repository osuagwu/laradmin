<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits\Billing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use BethelChika\Laradmin\Notifications\Notice;
use Laravel\Cashier\Exceptions\IncompletePayment;



trait StripePayment{
    
/**
     * SHow payment methods
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentMethods(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        $payment_methods=collect();
        $default_payment_method=null;
        if ($user->hasStripeId()) {
            $payment_methods = $user->paymentMethods();

            $default_payment_method=$user->defaultPaymentMethod();
        }

        $pageTitle='Payment methods';
        return view('laradmin::user.billing.payment_methods',compact('pageTitle','payment_methods','default_payment_method'));
    }

     /**
     * Create payment method
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentMethodCreate(Request $request){
        
        $user=$request->user();
        $this->authorize('update', $user);

        if(!config('laradmin.billing.stripe.secret')){
            return redirect()->route('user-billing')->with('warning','The action cannot be completed. Please contact us.');
        }

        $this->validate($request,[
            'is_default_payment_method'=>'nullable|integer|in:0,1',
        ]);


        if(!$user->hasStripeId()){
            $user->createAsStripeCustomer();
        }

        if(!$user->hasStripeId()){
            abort(403,'Could not find customer');
        }

        $is_default_payment_method=0;
        if($request->is_default_payment_method==1){
            $is_default_payment_method=1;
        }
        $cancel_url=route('user-billing-methods');

        $intent= $user->createSetupIntent();
        $pageTitle='Add new payment method';
        return view('laradmin::user.billing.stripe.payment_method_create',compact('pageTitle','intent','is_default_payment_method','cancel_url','user'));
    }
    
      /**
     * Store a payment method
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentMethodStore(Request $request){
        
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'payment_method'=>'required|string',
            'is_default_payment_method'=>'required|integer|in:0,1',
        ]);
        

        if($request->is_default_payment_method==1){
            $user->updateDefaultPaymentMethod($request->payment_method);
            $user->updateDefaultPaymentMethodFromStripe();
        }else{
            $user->addPaymentMethod($request->payment_method);
        }
        
        
        
        if($request->ajax()){
            return response()->json(['message'=>'done'],200,[],JSON_UNESCAPED_SLASHES);
        }

        return redirect()->route('user-billing-methods')->with('success','Done');

    }

    /**
     * Delete payment methods
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentMethodDestroy(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        

        $default_payment_method=$user->defaultPaymentMethod();
        if($default_payment_method->id==$request->payment_method){//TODO: Only do this check is customer as an active subscription 
            
            return back()->with('warning','You cannot delete a default payment method');
        }
        
        $payment_methods = $user->paymentMethods();
        foreach($payment_methods as $method){
            if($method->id==$request->payment_method){
                $method->delete();
                break;
            }
        }
        

        return back()->with('info','Done');

        
    }

    /**
     * Present form for a user to make a single arbitrary single payment
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function arbitraryPayment(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        
        $pageTitle='One-off payment';
        return view('laradmin::user.billing.stripe.arbitrary_payment',compact('pageTitle'));
    }

    /**
     * Present form for a user to make a single arbitrary single payment
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function arbitraryPaymentCreate(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        if(!config('laradmin.billing.stripe.secret')){
            return redirect()->route('user-billing')->with('warning','The action cannot be completed. Please contact us.');
        }

        $intent= $request->user()->createSetupIntent();
        $pageTitle='Make a one-ff payment';
        return view('laradmin::user.billing.stripe.arbitrary_payment_create',compact('pageTitle','intent','user'));
    }

    /**
     * Processing for a single arbitrary payment
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function arbitraryPaymentStore(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'payment_method'=>'required|string',
            'amount'=>'required|numeric',
        ]);
        // Stripe Accepts Charges In Cents...
        try {
            $payment = $request->user()->charge($request->amount*100, $request->payment_method);
        } 
        catch (IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('user-billing-invoices')]
            );
        }
        catch (Exception $e) {
            //
            Log::error(__METHOD__.' :: '.__LINE__.': Error making payment', $e->getMessage());
            return back()->with('danger','Error making payment'.$e->getMessage());
        }

        //dd($payment);
        
        $amount_paid=ucfirst($payment->currency).' '.($payment->amount_received/100);
        // TODO: send messages regarding the payment.
        $user->notify(new Notice('You have made a one-off payment of '.$amount_paid));

        $user->getSystemUser()->notify(new Notice($user->email.' (#'.$user->id.') '.' made an arbitrary one-off payment of '.$amount_paid));
        return redirect()->route('user-billing-pay-arb')->with('success','Payment was successful');

    }

}

