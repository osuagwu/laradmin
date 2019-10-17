
@extends('laradmin::user.layouts.app')
@section('content')

@push('head-styles')
{{--TODO: These styles are currently only used here, if these a need to move it elsewhere then we can move it to the main style file --}}
<style> 
    /* ======================== */
/*   Syed Sahar Ali Raza   	*/
/* ========================	*/
@import url(https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700italic,700,900italic,900);
@import url(https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900);
@import url(https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900);


#generic_price_table{
	/* background-color: #f0eded; */
}

/*PRICE COLOR CODE START*/
#generic_price_table .generic_content{
	background-color: #fff;
}

#generic_price_table .generic_content .generic_head_price{
	background-color: #f6f6f6;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content .head_bg{
	border-color: #e4e4e4 rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) #e4e4e4;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content .head span{
	color: #525252;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .sign{
    color: #414141;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .currency{
    color: #414141;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .cent{
    color: #414141;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .month{
    color: #414141;
}

#generic_price_table .generic_content .generic_feature_list ul li{	
	color: #a7a7a7;
}

#generic_price_table .generic_content .generic_feature_list ul li span{
	color: #414141;
}
#generic_price_table .generic_content .generic_feature_list ul li:hover{
	background-color: #E4E4E4;
	border-left: 5px solid {{$laradmin->assetManager->getBrands()['info']}};
}

#generic_price_table .generic_content .generic_price_btn a{
	border: 1px solid {{$laradmin->assetManager->getBrands()['info']}}; 
    color: {{$laradmin->assetManager->getBrands()['info']}};
} 

#generic_price_table .generic_content.active .generic_head_price .generic_head_content .head_bg,
#generic_price_table .generic_content:hover .generic_head_price .generic_head_content .head_bg{
	border-color: {{$laradmin->assetManager->getBrands()['info']}} rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) {{$laradmin->assetManager->getBrands()['info']}};
	color: #fff;
}

#generic_price_table .generic_content:hover .generic_head_price .generic_head_content .head span,
#generic_price_table .generic_content.active .generic_head_price .generic_head_content .head span{
	color: #fff;
}

#generic_price_table .generic_content:hover .generic_price_btn a,
#generic_price_table .generic_content.active .generic_price_btn a{
	background-color: {{$laradmin->assetManager->getBrands()['info']}};
	color: #fff;
} 
#generic_price_table{
	margin: 50px 0 50px 0;
    font-family: 'Raleway', sans-serif;
}
.row .table{
    padding: 28px 0;
}

/*PRICE BODY CODE START*/

#generic_price_table .generic_content{
	overflow: hidden;
	position: relative;
	text-align: center;
}

#generic_price_table .generic_content .generic_head_price {
	margin: 0 0 20px 0;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content{
	margin: 0 0 50px 0;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content .head_bg{
    border-style: solid;
    border-width: 90px 1411px 23px 399px;
	position: absolute;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content .head{
	padding-top: 40px;
	position: relative;
	z-index: 1;
}

#generic_price_table .generic_content .generic_head_price .generic_head_content .head span{
    font-family: "Raleway",sans-serif;
    font-size: 28px;
    font-weight: 400;
    letter-spacing: 2px;
    margin: 0;
    padding: 0;
    text-transform: uppercase;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag{
	padding: 0 0 20px;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price{
	display: block;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .sign{
    display: inline-block;
    font-family: "Lato",sans-serif;
    font-size: 28px;
    font-weight: 400;
    vertical-align: middle;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .currency{
    font-family: "Lato",sans-serif;
    font-size: 60px;
    font-weight: 300;
    letter-spacing: -2px;
    line-height: 60px;
    padding: 0;
    vertical-align: middle;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .price .cent{
    display: inline-block;
    font-family: "Lato",sans-serif;
    font-size: 24px;
    font-weight: 400;
    vertical-align: bottom;
}

#generic_price_table .generic_content .generic_head_price .generic_price_tag .month{
    font-family: "Lato",sans-serif;
    font-size: 18px;
    font-weight: 400;
    letter-spacing: 3px;
    vertical-align: bottom;
}

#generic_price_table .generic_content .generic_feature_list ul{
	list-style: none;
	padding: 0;
	margin: 0;
}

#generic_price_table .generic_content .generic_feature_list ul li{
	font-family: "Lato",sans-serif;
	font-size: 18px;
	padding: 15px 0;
	transition: all 0.3s ease-in-out 0s;
}
#generic_price_table .generic_content .generic_feature_list ul li:hover{
	transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;

}
#generic_price_table .generic_content .generic_feature_list ul li .fa{
	padding: 0 10px;
}
#generic_price_table .generic_content .generic_price_btn{
	margin: 20px 0 32px;
}

#generic_price_table .generic_content .generic_price_btn a{

    display: inline-block;
    font-family: "Lato",sans-serif;
    font-size: 18px;
    outline: medium none;
    padding: 12px 30px;
    text-decoration: none;
    text-transform: uppercase;
}

#generic_price_table .generic_content,
#generic_price_table .generic_content:hover,
#generic_price_table .generic_content .generic_head_price .generic_head_content .head_bg,
#generic_price_table .generic_content:hover .generic_head_price .generic_head_content .head_bg,
#generic_price_table .generic_content .generic_head_price .generic_head_content .head h2,
#generic_price_table .generic_content:hover .generic_head_price .generic_head_content .head h2,
#generic_price_table .generic_content .price,
#generic_price_table .generic_content:hover .price,
#generic_price_table .generic_content .generic_price_btn a,
#generic_price_table .generic_content:hover .generic_price_btn a{
	transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
} 
@media (max-width: 320px) {	
}

@media (max-width: 767px) {
	#generic_price_table .generic_content{
		margin-bottom:75px;
	}
}
@media (min-width: 768px) and (max-width: 991px) {
	#generic_price_table .col-md-3{
		float:left;
		width:50%;
	}
	
	#generic_price_table .col-md-4{
		float:left;
		width:50%;
	}
	
	#generic_price_table .generic_content{
		margin-bottom:75px;
	}
}


.price-heading{
    text-align: center;
}
.price-heading h1{
	color: #666;
	margin: 0;
	padding: 0 0 50px 0;
}

.bottom_btn{
	background-color: #333333;
    color: #ffffff;
    display: table;
    font-size: 28px;
    margin: 60px auto 20px;
    padding: 10px 25px;
    text-align: center;
    text-transform: uppercase;
}

.bottom_btn:hover{
	background-color: #666;
	color: #FFF;
	text-decoration:none;
}

</style>

@endpush
<section class="section section-subtle first-content-padding">
    <div class="container">
        @if(!$products)
            <h1 class="heading-1 page-title text-center">Plans</h1>
            <p class="text-center">No product available at this time.</p>
        @endif
       
        <div id="generic_price_table">  
        @foreach($products as $product)    
            
            
            <div class="row">
                <div class="col-md-12">
                    <!--PRICE HEADING START-->
                    <div class="price-heading clearfix">
                        <h1 class="heading-giant">{{ucfirst($product->name)}} <small>{{$product->metadata->tagline}}</small></h1>
                        <p class="padding-bottom-x7">{{$product->metadata->full_description}}</p>
                    </div>
                    <!--//PRICE HEADING END-->
                </div>
            </div>
            
            
                
            <!--BLOCK ROW START-->
            
            <div class="row">
                <!-- ********************************************************************* -->
                @foreach($plans as $plan)
                @continue($product->id!=$plan->product)
                <form class="col-md-6 form-horizontal" id="{{$plan->id}}-form" action="{{route('user-billing-sub1')}}" method="post">
                    @csrf
            
                    <!--PRICE CONTENT START-->
                    <div class="generic_content clearfix {{$loop->first?'active':''}}">
                        
                        <!--HEAD PRICE DETAIL START-->
                        <div class="generic_head_price clearfix">
                        
                            <!--HEAD CONTENT START-->
                            <div class="generic_head_content clearfix">
                            
                                <!--HEAD START-->
                                <div class="head_bg"></div>
                                <div class="head">
                                    <span>{{ucfirst($plan->nickname)}}</span>
                                </div>
                                <!--//HEAD END-->
                                
                            </div>
                            <!--//HEAD CONTENT END-->
                            
                            <!--PRICE START-->
                            <div class="generic_price_tag clearfix">	
                                <span class="price">
                                    <span class="sign">{{str_replace(['0','.'],'',\Laravel\Cashier\Cashier::formatAmount(0,$plan->currency))}}</span>
                                    <span class="currency">{{str_replace('£','',explode('.',\Laravel\Cashier\Cashier::formatAmount($plan->amount,$plan->currency))[0])}}</span>
                                    @if(count(explode('.',\Laravel\Cashier\Cashier::formatAmount($plan->amount,$plan->currency)))>1){{--If q currency does not have decimals then do not try to print one--}}
                                        <span class="cent">.{{explode('.',\Laravel\Cashier\Cashier::formatAmount($plan->amount,$plan->currency))[1]}}</span>
                                    @endif
                                    <span class="month">/{{$plan->interval_count>1?$plan->interval_count:''}}{{$plan->interval}}</span>
                                </span>
                            </div>
                            <!--//PRICE END-->
                            
                        </div>                            
                        <!--//HEAD PRICE DETAIL END-->
                        
                        <!--FEATURE LIST START-->
                        <div class="generic_feature_list">
                            <ul>
                                @foreach(explode(',',$plan->metadata->feature_list) as $feature)
                                    @if(count(explode(' ',$feature))>1)
                                        <li><span>{{explode(' ',trim($feature))[0]}}</span>{{-- get only the first word--}}
                                        {{implode(' ',array_slice(explode(' ',trim($feature)),1))}}</li>{{--get the rest of the words without the first--}}
                                        
                                    @else 
                                        <li><span>{{$feature}}</span></li>
                                    @endif
                                
                                @endforeach

                            </ul>
                        </div>
                        <!--//FEATURE LIST END-->

                        <input type="hidden" class="form-control" name="plan" value="{{$plan->id}}">
                        <div class="form-group">
                            <label class="col-xs-offset-3 col-xs-3 control-label" for="plan-select-quantity">Quantity</label>
                            <div class="col-xs-3">
                                <select class="form-control" name="quantity"  id="plan-select-quantity">
                                    @for($i=1;$i<=config('laradmin.billing.stripe.subscription.max_quantity',1);$i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            
                        </div>
                        <!--BUTTON START-->
                        <div class="generic_price_btn clearfix">
                            <a class="" href="#" onclick="$('#{{$plan->id}}-form').submit()">Select</a>
                        </div>
                        <!--//BUTTON END-->
        
                        
                        
                    </div>
                    <!--//PRICE CONTENT END-->
                        
                </form>
                @endforeach 
                <!-- ********************************************************************* -->

               
            </div>	
            <!--//BLOCK ROW END-->
                
            
                    
            <footer>
                <a class="bottom_btn" href="{{route('user-billing-subs')}}">My subscriptions</a>
            </footer>
        @endforeach
        </div>
        

    </div>
</section> 
@endsection
