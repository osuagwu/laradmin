
@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')

    

    @foreach($products as $product)
 
        <form class="form-horizontal" action="{{route('user-billing-sub1')}}" method="post">
            @csrf
            

            <h2 class="heading-2 heading-underline underscore">{{ucfirst($product->name)}} <small>{{$product->metadata->tagline}}</small></h2>
            <p>{{$product->metadata->full_description}}</p>
            <div class="form-group">
                <label class="col-md-1 control-label" for="plan-select-{{$product->id}}">Plan</label>
                <div class="col-md-6">
                    <select class="form-control" name="plan" id="plan-select-{{$product->id}}">
                        @foreach($plans as $plan)
                            @continue($product->id!=$plan->product)
                            <option value="{{$plan->id}}">{{ucfirst($plan->nickname)}} </option>
                        @endforeach
                    </select>
                </div>
                   
            </div> 

            <div class="form-group padding-bottom-x7">
                <label class="col-md-1 control-label" for="plan-select-quantity">Quantity</label>
                <div class="col-md-6">
                    <select class="form-control" name="quantity"  id="plan-select-quantity">
                        @for($i=1;$i<=config('laradmin.billing.stripe.subscription.max_quantity',1);$i++)
                            <option value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-1">
                    <button class="btn btn-primary " type="submit">Continue</button>
                </div>
            </div>
        </form>
    @endforeach

@endsection
