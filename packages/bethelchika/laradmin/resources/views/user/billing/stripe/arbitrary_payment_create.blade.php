@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
<div class="sub-content with-padding no-border no-elevation">
   
    <form class="form-horizontal" id="payment-form" action="{{route('user-billing-pay-arb-c')}}" method="POST">
        @csrf
        <div class="form-group">
            <div class="col-md-6">
                <div class="sub-content with-padding">
                    @include('laradmin::form.components.input_text',['name'=>'amount','value'=>'','label'=>'Amount '.str_replace(['0','.'],'',\Laravel\Cashier\Cashier::formatAmount(0)),'placeholder'=>'Amount ('.(strtoupper(config('laradmin.billing.stripe.currency'))).')'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="sub-content with-padding">
                    @include('laradmin::partials.billing.stripe.single_payment_method',['intent'=>$intent,'payment_form_id'=>'payment-form'])
                </div>
            </div>
        </div>
    </form>
       
</div>
@endsection
