@if (!Auth::guest())
    @if (Auth::user()->status==-1)
        <div class="alert alert-warning alert-dismissable fade in padding-top-x3">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <h4>Email address confirmation</h4>
            <p>Please confirm your email address using the link sent to you by email.  </p>
            
        <p><a class="btn btn-warning" href="{{route('send-email-confirmation')}}">Resend email</a></p></div>
    @endif
@endif
