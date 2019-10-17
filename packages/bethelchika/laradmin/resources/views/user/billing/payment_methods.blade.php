@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
    @include('laradmin::user.billing.partials.top')
    
    <div class="row">
        <div class="col-md-8">
            <h3 class="heading-3">Stripe default payment methods (for invoices and subscriptions)</h3>
                
                @if($default_payment_method)
                <p class="text-right "> <a href="{{route('user-billing-method-create',['is_default_payment_method'=>1])}}"><small class="fas fa-pen"></small> Update default payment method</a></p>
                    <div class="sub-content with-padding no-border no-elevation">
                        <div class="media">
                            <div class="media-left media-middle">
                                <span class="heading-huge text-muted">
                                    @switch($default_payment_method->type)
                                        @case('card')
                                            <i class="far fa-credit-card"></i>
                                            @break
                                        @default
                                            <i class="fas fa-money-bill-alt"></i>
                                    @endswitch
                                </span>
                            </div>
                            <div class="media-body">
                                <h5 class="heading-5 media-heading">{{ucfirst($default_payment_method->card->brand)}} {{$default_payment_method->card->funding}} 
                                    
                                </h5>
                                <span class="fainted-05">*******{{$default_payment_method->card->last4}}</span>
                                <small class="fainted-05">exp: {{$default_payment_method->card->exp_month}}/{{$default_payment_method->card->exp_year}} </small>
                                
                            </div>
                            
                        </div>
                        
                        
                    </div>
                @else
                    <p class="alert alert-warning "> You do not have Stripe payment method for invoices and subscriptions. <a href="{{route('user-billing-method-create',['is_default_payment_method'=>1])}}"><small class="fas fa-plus"></small> New payment method</a></p>
                @endif




            <h3 class="heading-3">Stripe payment methods</h3>
            <p class="text-right">
                <a href="{{route('user-billing-method-create')}}" ><small class="fas fa-plus"></small> New payment method</a>
            </p>
            @unless(count($payment_methods))
                <p class="alert alert-warning "> You do not have a payment method. <a href="{{route('user-billing-method-create')}}"><small class="fas fa-plus"></small> New payment method</a></p>
            @endunless
                
            
            @foreach($payment_methods as $method)
                <div class="sub-content with-padding no-border no-elevation">
                    <div class="media">
                        <div class="media-left media-middle">
                            <span class="heading-huge text-muted">
                                @switch($method->type)
                                    @case('card')
                                        <i class="far fa-credit-card"></i>
                                        @break
                                    @default
                                        <i class="fas fa-money-bill-alt"></i>
                                @endswitch
                            </span>
                        </div>
                        <div class="media-body">
                            <h5 class="heading-5 media-heading">{{ucfirst($method->card->brand)}} {{$method->card->funding}}
                                @if($default_payment_method and $default_payment_method->id==$method->id)
                                    <span  style="font-size:50%" class="badge" title="Default payment method for invoices and subscriptions">Default method</span>
                                    
                                @endif
                            </h5>
                            <span class="fainted-05">*******{{$method->card->last4}}</span>
                            <small class="fainted-05">exp: {{$method->card->exp_month}}/{{$method->card->exp_year}} </small>
                            
                        </div>
                        <div class="media-right media-middle">
                            @if(!$default_payment_method or ($default_payment_method and $default_payment_method->id!=$method->id))
                                <form style="display:inline"  action="{{route('user-billing-method-create')}}" method="POST">
                                    @method('PUT')
                                    @csrf()
                                    <input type="hidden" name="payment_method" value="{{$method->id}}">
                                    <input type="hidden" name="is_default_payment_method" value="1">
                                    <button type="submit" style="background-color:transparent; border:none"><small>Default </small></button> 
                                </form>
                            @else
                                <a title="Update default payment method" href="{{route('user-billing-method-create',['is_default_payment_method'=>1])}}"><small class="fas fa-pen"></small> </a>
                            @endif
                            
                        </div>
                        <div class="media-right media-middle">
                            <form style="display:inline"  action="{{route('user-billing-method-create')}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment method?')">
                                @method('DELETE')
                                @csrf()
                                <input type="hidden" name="payment_method" value="{{$method->id}}">
                                <button type="submit" class="text-danger" style="background-color:transparent; border:none"><i class="fas fa-times"></i></small></button> 
                            </form>
                        </div>
                    </div>
                    
                    
                </div>
            @endforeach
        </div>
    </div>
    <br>
    <br>
@endsection
