
@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
    <p>Select a plan you would like to swap to:</p>

    <form class="form-horizontal" action="{{route('user-billing-sub-swap',[$subscription->name])}}" method="post">
        @csrf
        @method('put')

        @foreach($products as $product)
                <h4 class="heading-4">{{ucfirst($product->name)}}</h4>
                <p>{{$product->description}}</p>
            <div class="form-group">
                <label class="col-md-1 control-label" for="plan-select-{{$product->id}}">Plan</label>
                <div class="col-md-6">
                    <select class="form-control" name="plan" id="plan-select-{{$product->id}}">
                        @foreach($plans as $plan)
                            @continue($product->id!=$plan->product)
                            <option value="{{$plan->id}}">{{$plan->nickname}}</option>
                        @endforeach
                    </select>
                </div>
                   
            </div> 
        @endforeach
        <p class="col-md-6 col-md-offset-1">
            <a href="{{route('user-billing-subs')}}" class="btn btn-subtle">Cancel</a>
            <button class="btn btn-primary " type="submit">Swap to selected plan</button>
        </p>
    </form>

@endsection
