<?php
namespace BethelChika\Laradmin\Billing\Stripe;

use Illuminate\Support\Facades\Cache;

class Stripe{
    /**
     * This class provides some version of API calls for Stripe applying Laravel's Cache
     */

     

     /**
     * The Stripe plans cache name
     *
     * @var string
     */
    public static $stripePlansCache='stripe:plans';

    /**
     * The Stripe products cache name
     *
     * @var string
     */
    public static $stripeProductsCache='stripe:products';

    

    /**
     * Get All plans
     *
     * @return \Stripe\Collection
     */
    public function allPlans(){
        if(!config('laradmin.billing.stripe.secret')){
            return [];
        }

        $seconds=10080;
        $plans = Cache::remember(static::$stripePlansCache, $seconds, function () {
            \Stripe\Stripe::setApiKey(config('laradmin.billing.stripe.secret'));
            $plans=\Stripe\Plan::all();
            return $plans;
            
        });

        // if($plans){
        //     $plans=new \Stripe\Collection();
        // }

        return $plans;
    }

    /**
     * Get a Plan
     * @param $plan_id 
     * @return \Stripe\Plan
     */
    public function retrievePlan($plan_id){
        $plan=null;
        foreach($this->allPlans() as $p){
            if($p->id==$plan_id){
                $plan=$p;
                break;
            }
        }

        return $plan;
    }


       /**
     * Get all products
     *
     * @return \Stripe\Collection
     */
    public function allProducts(){

        if(!config('laradmin.billing.stripe.secret')){
            return [];
        }

        $seconds=10080;
        $products = Cache::remember(static::$stripeProductsCache, $seconds, function () {
            \Stripe\Stripe::setApiKey(config('laradmin.billing.stripe.secret'));
            $products=\Stripe\Product::all();
            
            return $products;
        });


        return $products;
    }

    /**
     * Get a Product
     * @param $product_id 
     * @return \Stripe\Product
     */
    public function retrieveProduct($product_id){
        $product=null;
        foreach($this->allProducts() as $p){
            if($p->id==$product_id){
                $product=$p;
                break;
            }
        }

        return $product;
    }

    /**
     * Forces the product cache to refresh
     *
     * @return void
     */
    public function allProductsRefresh(){
         Cache::forget(Stripe::$stripeProductsCache);
         $this->allProducts();
    }

        /**
     * Forces the plan cache to refresh
     *
     * @return void
     */
    public function allPlansRefresh(){
        Cache::forget(Stripe::$stripePlansCache);
        $this->allPlans();
   }

}