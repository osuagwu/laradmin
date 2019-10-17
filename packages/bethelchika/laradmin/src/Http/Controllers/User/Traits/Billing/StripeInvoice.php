<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\Billing;

use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;

trait StripeInvoice
{


    /**
     * Show a customer Invoices
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function invoiceIndex(Request $request)
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $invoices =collect();
        if($user->hasStripeId()){
            $invoices = $user->invoicesIncludingPending();
        }

        $pageTitle = 'Invoice';
        return view('laradmin::user.billing.stripe.invoice.index', compact('pageTitle', 'invoices'));
    }


    /**
     * Show a customer Invoices
     * @param Request $request
     * @param string $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request, $invoice_id)
    {
        $user = $request->user();
        $this->authorize('update', $user);
        

        $invoice=$user->findInvoice($invoice_id);
        if(!$invoice){
            return back()->with('warning','Could not locate invoice');
        }

        $subscription=Subscription::where('stripe_id',$invoice->subscription)->first();

        \Stripe\Stripe::setApiKey(config('laradmin.billing.stripe.secret'));
        $plan=\Stripe\Plan::retrieve($subscription->stripe_plan);
        
        $product=null;
        if($plan){
            $product=\Stripe\Product::retrieve($plan->product);
        }

        $product_info='Subscription';
        if($product){
            $product_info=$product->statement_descriptor;
        }

        return $request->user()->downloadInvoice($invoice_id, [
            'vendor' => config('app.name'),
            'product' => $product_info,
        ]);
    }
}
