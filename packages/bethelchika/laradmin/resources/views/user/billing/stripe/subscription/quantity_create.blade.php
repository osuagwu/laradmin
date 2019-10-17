
@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
    <p>Select a quantity you would like:</p>

    <form class="form-horizontal" action="{{route('user-billing-sub-quantity',[$subscription->name])}}" method="post">
        @csrf
        @method('put')

        <div class="form-group padding-bottom-x7">
            <label class="col-md-1 control-label" for="plan-select-quantity">Quantity</label>
            <div class="col-md-6">
                <select class="form-control" name="quantity"  id="plan-select-quantity">
                    @for($i=1;$i<=50;$i++)
                        <option value="{{$i}}">{{$i}}</option>
                    @endfor
                </select>
            </div>
            
        </div>
        <p class="col-md-6 col-md-offset-1">
            <a href="{{route('user-billing-subs')}}" class="btn btn-subtle">Cancel</a>
            <button class="btn btn-primary " type="submit">Update with the selected quantity</button>
        </p>
    </form>

@endsection
