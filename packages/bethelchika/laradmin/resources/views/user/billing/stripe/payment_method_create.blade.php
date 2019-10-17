@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')

<div class="sub-content with-padding no-border no-elevation"  >
    <div class="row ">
        <div class="col-md-6 ">
            <form class="form-horizontal" >
                @include('laradmin::partials.billing.stripe.payment_method',compact('intent','is_default_payment_method','cancel_url'))           
            </form>
        </div>
    </div>
</div>

@endsection
