@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
@include('laradmin::user.billing.partials.top')
<p class="text-right" > <a href="{{route('user-billing-sub1')}}" class="btn btn-secondary btn-sm"> Add a subscription plan</a></p>

@if(count($subscriptions))
    <p> You have {{count($subscriptions)}} subscriptions</p>
    @foreach($subscriptions as $subscription)
        @foreach($plans as $plan)
            @continue($plan->id!=$subscription->stripe_plan)
            @foreach($products as $product)
                @continue($product->id!=$plan->product)

                {{--Now list the subscription --}}
                <div class="media" style="margin-bottom:40px;">
                    {{--  <div class="media-left media-middle">
                        <a href="#">
                            <span class="media-object heading-huge">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                        </a>
                    </div>  --}}
                    <div class="media-body">
                        <h5 class="media-heading ">{{$product->name}} <small>{{$plan->nickname}}</small></h5>
                        <small class="fainted-06" style="font-size:70%">Plan id: {{$plan->id}} Subscription id: {{$subscription->stripe_id}}</small>
                        <small class="fainted-07 " style="font-size:70%">(On statement as: {{$product->statement_descriptor}})</small>
                        <div>
                            {{$product->description}}
                            
                        </div>
                        <div>
                            <a href="{{ route('user-billing-sub-swap', $subscription->name) }}"><small>Swap plan</small></a>
                            <a href="{{ route('user-billing-sub-quantity', $subscription->name) }}"><small>Update quantity <span class="badge" style="font-size:60%">{{$subscription->quantity}}</span> </small></a>
                            
                            @if($subscription->onGracePeriod())
                                <form style="display:inline" action="{{route('user-billing-sub-action',[$subscription->name,'resume'])}}" method="POST" >
                                    @csrf
                                    @method('put')
                                    <button class="text-info" type="submit" style="background-color:transparent;border:none;" ><small> Resume </small></button>
                                </form>
                            @else
                                @if($subscription->cancelled())
                                    {{--<p class="alert alert-danger"> This has been cancelled!</p>--}}{{--Not sure if this is required, hence comment it out for now --}}
                                @else
                                    <form style="display:inline" action="{{route('user-billing-sub-action',[$subscription->name,'cancel'])}}" method="POST" onsubmit="return confirm('Are you sure you want to cancel?')">
                                        @csrf
                                        @method('put')
                                        <button class="text-danger" type="submit" style="background-color:transparent;border:none;" ><small> <i class="fas fa-times"></i> Cancel </small></button>
                                    </form>
                                @endif
                            @endif
                            
                            <form style="display:inline" action="{{route('user-billing-sub-action',[$subscription->name,'sync-stripe-status'])}}" method="POST" >
                                @csrf @method('put')
                                <button class="text-muted" type="submit" style="background-color:transparent;border:none;" ><small> <i class="fas fa-sync"></i> Refresh </small></button>
                            </form>
                        </div>
                        
                        @if($subscription->onTrial())<span class="label label-info fainted-07">Trial until {{$subscription->trial_ends_at}}</span>@endif
                        <span class="label label-{{($subscription->stripe_status==='active')?'success':'danger'}} fainted-07">{{ucfirst($subscription->stripe_status)}}</span>
                        @if($subscription->onGracePeriod())<span class="label label-warning fainted-07">Grace period</span>@endif
                        @if($subscription->hasIncompletePayment())
                            <span class="label label-danger fainted-07">Incomplete payment
                            </span>
                            <a class="text-danger" href="{{ route('cashier.payment', $subscription->latestPayment()->id) }}">
                                 &nbsp; Please confirm your payment
                            </a>
                        @endif
                        @if($subscription->ends_at)<span class="label label-info fainted-07">Ends: {{$subscription->ends_at}}</span>@endif

                       
                    </div>
                </div>
                {{-- end item --}}

            @endforeach
        @endforeach
    @endforeach
    <p> You have {{count($subscriptions)}} subscriptions</p>
@else
    <p class="alert alert-warning"> You do not have a subscription. <a href="{{route('user-billing-sub1')}}" > Subscribe to a plan</a></p>
@endif


@endsection
