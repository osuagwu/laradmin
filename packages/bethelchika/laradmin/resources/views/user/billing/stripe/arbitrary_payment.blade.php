@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
@include('laradmin::user.billing.partials.top')

<a class="btn btn-primary" href="{{route('user-billing-pay-arb-c')}}">Make a single payment</a>
@endsection
