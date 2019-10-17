@extends('laradmin::user.layouts.app')

@section('content')

<section role="banner" class="section section-success section-title  padding-top-x7 padding-bottom-x7">
    <div class="container">
        <h1 class="page-title heading-1"> <i class="fas fa-lock" ></i> {{$pageTitle}}</h1>
        <p>Thanks for verifying your identity.</p>
    </div>
    
</section>
<section role="banner" class="section section-default section-title  padding-top-x7 padding-bottom-x7">
    <div class="container">
        <p>Verification was successfull.</p>
        <a class="btn btn-success" href="{{route('user-auth-v-done')}}"> Proceed</a>
    </div>
</section>
@endsection
