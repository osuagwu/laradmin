<?php

namespace BethelChika\Laradmin\Billing\Stripe\Http\Controllers;

use BethelChika\Laradmin\Billing\Stripe\Stripe;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    // Note: The methods of these function are called by the parent class.

    /**
     * Handle Product created
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleProductCreated($payload)
    {
        (new Stripe)->allProductsRefresh();
        return $this->successMethod();
    }

    /**
     * Handle Product deleted
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleProductDeleted($payload)
    {
        (new Stripe)->allProductsRefresh();
        return $this->successMethod();
    }

    /**
     * Handle Product updated
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleProductUpdated($payload)
    {
        (new Stripe)->allProductsRefresh();
        return $this->successMethod();
    }

        /**
     * Handle Plan created
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handlePlanCreated($payload)
    {
        (new Stripe)->allPlansRefresh();
        return $this->successMethod();
    }

    /**
     * Handle Plan deleted
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handlePlanDeleted($payload)
    {
        (new Stripe)->allPlansRefresh();
        return $this->successMethod();
    }

    /**
     * Handle Plan updated
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handlePlanUpdated($payload)
    {
        (new Stripe)->allPlansRefresh();
        return $this->successMethod();
    }
}