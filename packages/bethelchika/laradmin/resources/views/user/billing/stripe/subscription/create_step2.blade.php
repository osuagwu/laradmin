@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
<p class="well" > <strong class="label label-info">Selected plan</strong> {{ucfirst($product->name)}} - {{ucfirst($plan->nickname)}} &times; <small class="">{{$quantity}}</small>  <small>Unit price {{\Laravel\Cashier\Cashier::formatAmount($plan->amount,$plan->currency)}} Payment Frequency: Every {{$plan->interval_count>1?$plan->interval_count:''}}{{$plan->interval}}</small></p>
    <form class="form-horizontal" action="{{route('user-billing-sub2')}}" method="post">
        @csrf
        <input type="hidden" name="plan" value="{{$plan->id}}">
        <input type="hidden" name="quantity" value="{{$quantity}}">

        
        <div class="row"> 
            @if(count($payment_methods))
            <div class="col-md-6">
                <h4 class="heading-4">Select from save payment cards</h4>
                
                    @foreach ($payment_methods as $m)
                        <div class="media">
                            <div class="media-left media-middle">
                                <span class="media-object ">
                                    <input type="radio" name="payment_method" value="{{$m->id}}" id="payment-method-radio-{{$m->id}}" @if($default_payment_method and $default_payment_method->id==$m->id) checked @endif onchange="selectedSavedPaymentMethod()">
                                </span>
                            </div>
                            <label class="media-body" for="payment-method-radio-{{$m->id}}"> 
                                <span class="media-heading ">
                                    {{ucfirst($m->card->brand).' '.$m->card->funding}} 
                                    @if($default_payment_method and $default_payment_method->id==$m->id) <small class="label label-info fainted-06">Default</small>@endif
                                </span>
                                <br>
                                <small class="fainted-05">ending in {{$m->card->last4}} exp&nbsp;{{$m->card->exp_month.'/'.$m->card->exp_year}}</small>
                                
                            </label>
                        </div>
                    @endforeach 
            </div>
            @endif

            <div class="col-md-6">
                <h4 class="heading-4">{{count($payment_methods)?'Or add a new payment card':'Add a new payment card to continue'}}</h4>
                <label><input type="radio" name="payment_method" value="" id="stripe-new-payment-method-radio"> Add a new card</label>
                <div id="stripe-subscription-new-payment-method-box" style="display:none">
                    <p class="alert alert-success verified" style="display:none"></p>
                    @include('laradmin::partials.billing.stripe.payment_method',['intent'=>$intent,'auto_init'=>0])
                </div>
            </div>
            
            
        </div>

        <br>
        <div class="text-center">
            <a href="{{route('user-billing-sub1')}}"> <i class="fas fa-chevron-left"></i> Change plan</a> &nbsp;&nbsp;
            <button class="btn btn-primary" type="submit"id="stripe-subscription-submit-btn" >Continue</button>
        </div>
    </form>

    @push('footer-scripts')
        <script>
            let STRIPE_NEW_PAYMENT_METHOD_INITIALISED=false;

            $("#stripe-new-payment-method-radio").change(function() {
                let $ele=$(this);
                if($ele.prop('checked') && $ele.prop('value')=='') {
                    $('#stripe-subscription-submit-btn').prop("disabled", true).css({visibility:'hidden'});
                }

                $('#stripe-subscription-new-payment-method-box').show('slow');

                if(!STRIPE_NEW_PAYMENT_METHOD_INITIALISED){
                    STRIPE_NEW_PAYMENT_METHOD_INITIALISED=true;
                    stripeNewPaymentmehtod();
                }
            });

            /**
             * Init the new payment method form 
             */
            function stripeNewPaymentmehtod(){
                initStripePaymentMethod(function(paymentMethod){
                    $('#stripe-new-payment-method-radio').val(paymentMethod);
                    $('#stripe-subscription-new-payment-method-box .verified').text('Verified, please continue below').fadeIn('slow');
                    $('#stripe-subscription-submit-btn').prop("disabled", false).css({visibility:'visible'});
                });
            }

            /**
             * Allows to adjust settings when a saved card is selected 
             */
            function selectedSavedPaymentMethod(){
                $('#stripe-subscription-submit-btn').prop("disabled", false).css({visibility:'visible'});
                $('#stripe-subscription-new-payment-method-box').hide('slow');
            }
        </script>
    @endpush
@endsection

