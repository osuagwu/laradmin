
{{-- Present views and scripts to enable obtaining 

[INPUT]
$intent->client_secret PaymentIntent STRIPE  Object for payment intent. 
$is_default_payment_method Integer {0,1} [optional] Is this for a default payment method
$auto_init {0,1(default)} Should the payment method be initialised automatically. If=0, then you have to manually call the js function, initStripePaymentMethod(....) after including this view. See initStripePaymentMethod() for input parameters.
$calcel_url String [Optional] The url to navigate when the cancel button is clicked. 
$user User Logged in user object
--}}
<div id="stripe-get-payment-method-box" >{{-- Note that this view should not include a form element to prevent form nexting in the parent view--}}

    <p style='display:none' class="alert alert-danger" id="billing-payment-method-error" type="text"></p>
    <p style='display:none' class="alert alert-success" id="billing-payment-method-success" type="text"></p>

    <input type="hidden" name="billing-methods-redirect" id="billing-methods-redirect" value='{{route('user-billing-methods')}}'>
    <input type="hidden" name="billing-methods-create" id="billing-methods-create" value='{{route('user-billing-method-create')}}'>
    <input type="hidden" name="billing-methods-is-default" id="billing-methods-is-default" value='{{$is_default_payment_method??'0'}}'>


     @include('laradmin::form.components.input_text',['name'=>'card-holder-name','value'=>$user->name,'label'=>'Name','placeholder'=>"Name on card"])
     @include('laradmin::form.components.input_text',['name'=>'card-holder-email','value'=>$user->email,'label'=>'Email','placeholder'=>"Email"])
     @include('laradmin::form.components.input_text',['name'=>'card-holder-phone','value'=>'','label'=>'Phone','placeholder'=>"Full phone number"])
    
    @include('laradmin::form.components.input_text',['name'=>'card-holder-line1','value'=>'','label'=>'Address','placeholder'=>"Address"])
    @include('laradmin::form.components.input_text',['name'=>'card-holder-city','value'=>'','label'=>'City','placeholder'=>"City"])
    @include('laradmin::form.components.input_text',['name'=>'card-holder-postal-code','value'=>'','label'=>'Postal code','placeholder'=>"Postal/Zip code"])
    
    @include('laradmin::form.components.input_select',['name'=>'card-holder-country','value'=>$user->country,'label'=>'Country','options'=>__( 'laradmin::list_of_countries')])

    <!-- Stripe Elements Placeholder -->
    @include('laradmin::form.components.input_stripe_card',['name'=>'card-element','value'=>'', 'label'=>'Card details','help'=>'Enter card number, then expiry, then the security number which is the last 3 digits at the back of your card.'])
    
    <p> Your card/information will be securely stored by <a href="http://stripe.com" target="_blank">Stripe</a>, our payment provider.</p>
    @if(isset($cancel_url))<a href="{{$cancel_url}}" class=text-danger> <i class="fas fa-times"></i> Cancel </a>@endif
    <button type="button" class="btn btn-primary btn-sm" id="card-button" data-secret="{{ $intent->client_secret }}" disabled>
        <i class="fas fa-check"></i> Verify card
    </button>
    
</div>

            

@push('footer-scripts')
    <script>
        
        /**
         * Get the payment details using stripe.
         * 
         * @param {callback} cbSuccess A function to call after a successful card validation. The stripe payment id is provided as an argument. If not provided, The card details will be saved and page will be redirected based on the value of #billing-methods-redirect input element.
         * @param {callback} cbError a function to call after a fail card validation. An error message is provided as an argument. If not provided default error presentation will be used.
         * @returns void
         */
        function initStripePaymentMethod(cbSuccess,cbError){
            //Custome elements.
            const errorEle=$('#billing-payment-method-error');
            const successEle=$('#billing-payment-method-success');

            // Enable form
            $("#stripe-get-payment-method-box :input").prop("disabled", false);

            // Card style for later
            let elementOptions={
                hidePostalCode :true,
                iconStyle: 'solid',
                style: {
                    base: {
                        //iconColor: '#c4f0ff',
                       // color: '#fff',
                        //fontWeight: 500,
                        //fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                        //fontSize: '16px',
                        //fontSmoothing: 'antialiased',
                        //':-webkit-autofill': {
                        //color: '#fce883',
                        //},
                        //'::placeholder': {
                        //color: '#87BBFD',
                        //},
                    },
                    invalid: {
                        //iconColor: '#FFC7EE',
                        //color: '#FFC7EE',
                    },
                },
            };
            
            

            //Gather payment details *****************STRIPE***************
            const stripe = Stripe('{{config('laradmin.billing.stripe.key')}}');

            const elements = stripe.elements();
            const cardElement = elements.create('card',elementOptions);

            cardElement.mount('#card-element');

            // Verification *****************STRIP***************
            const cardHolderName = document.getElementById('card-holder-name');

            const cardHolderEmail=document.getElementById('card-holder-email');
            const cardHolderPhone=document.getElementById('card-holder-phone');
            const cardHolderCity=document.getElementById('card-holder-city');
            const cardHolderCountry=document.getElementById('card-holder-country');
            const cardHolderLine1=document.getElementById('card-holder-line1');
            const cardHolderPostalCode=document.getElementById('card-holder-postal-code');
                               


            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;

            cardButton.addEventListener('click', async (e) => {
                errorEle.css({display:'none'});
                const { setupIntent, error } = await stripe.handleCardSetup(
                    clientSecret, cardElement, {
                        payment_method_data: {
                            billing_details: { // These are all  optional so cosider reducing the details collected from user
                                name: cardHolderName.value,//Optional
                                email: cardHolderEmail.value,//Optional
                                phone: cardHolderPhone.value,//Optional
                                address: {
                                    city:cardHolderCity.value,//Optional
                                    country:cardHolderCountry.value,//Optional
                                    line1:cardHolderLine1.value,//Optional 
                                    //line2:city:cardHolderLine2.value,////Optional
                                    postal_code:cardHolderPostalCode.value,//Optional
                                },
                             }
                        }
                    }
                );
                //************************************************************
                if (error) {
                    // Display "error.message" to the user...
                    if (typeof cbError === "function") {
                        cbError(cbError);
                        return;
                    }
                    
                    errorEle.text(error.message);
                    errorEle.css({display:'block'});
                    
                    
                } else {
                    // The card has been verified successfully...


                    //Disable form
                    $("#stripe-get-payment-method-box :input").prop("disabled", true);


                    if (typeof cbSuccess === "function") {
                        cbSuccess(setupIntent.payment_method);
                        return;
                    }

                    
                    // Save the details
                    successEle.html('Saving ...');
                    successEle.css({display:'block'});

                    url=$('#billing-methods-create').val();
                    data_out={
                        payment_method:setupIntent.payment_method,
                        is_default_payment_method:$('#billing-methods-is-default').val(),
                        _token:$('meta[name="csrf-token"]').attr('content'),
                    };
                    $.post(url,data_out)
                        .done(data => {
                            window.location.replace($('#billing-methods-redirect').val());
                        }).fail((jqXHR, textStatus,err) => {
                            const errorEle=$('#billing-payment-method-error')
                            errorEle.html('Error saving card: ' +err);
                            errorEle.css({display:'block'})
                        }).always((jqXHR, textStatus) => {
                            
                        });
                }
            });
        }
        
    </script>

    {{-- Auto init --}}
    @if(!isset($auto_init) or $auto_init))
        <script>
            initStripePaymentMethod();
        </script>
    @endif
@endpush

