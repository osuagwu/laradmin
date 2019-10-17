{{--
    This partial allows you to process payment for a single charge using laravel cashier/strip.

    INPUT:
    $payment_form_id String The id of a form where the payment method should be added to. This could be a form containing the product details that a customer is trying to pay for. The form can later be submitted together with a payment method.
    $auto_submit int {0,1(Default)} The payment form will be auto submitted when true.
    $intent STRIPE INTENT
    $user UserLogged in user object
    USAGE:
        <form id="payment-form" action="..." method="POST">
            <input name="product_id" value="34">
        </form>
        @include('laradmin::user.billing.stripe.single_payment_method',['intent'=>$intent,'payment_form_id'=>'form-form'])

    --}}

            
<p style='display:none' class="alert alert-danger" id="billing-payment-method-error" type="text"></p>
<p style='display:none' class="alert alert-success" id="billing-payment-method-success" type="text"></p>




@include('laradmin::form.components.input_text',['name'=>'card-holder-name','value'=>$user->name,'label'=>'Name','placeholder'=>"Name on card"])
@include('laradmin::form.components.input_text',['name'=>'card-holder-email','value'=>$user->email,'label'=>'Email','placeholder'=>"Email"])
@include('laradmin::form.components.input_text',['name'=>'card-holder-phone','value'=>'','label'=>'Phone','placeholder'=>"Full phone number"])

@include('laradmin::form.components.input_text',['name'=>'card-holder-line1','value'=>'','label'=>'Address','placeholder'=>"Address"])
@include('laradmin::form.components.input_text',['name'=>'card-holder-city','value'=>'','label'=>'City','placeholder'=>"City"])
@include('laradmin::form.components.input_text',['name'=>'card-holder-postal-code','value'=>'','label'=>'Postal code','placeholder'=>"Postal/Zip code"])

@include('laradmin::form.components.input_select',['name'=>'card-holder-country','value'=>$user->country,'label'=>'Country','options'=>__( 'laradmin::list_of_countries')])

<!-- Stripe Elements Placeholder -->
@include('laradmin::form.components.input_stripe_card',['name'=>'card-element','value'=>'', 'label'=>'Card details','help'=>'Enter card number, then expiry, then the security number which is the last 3 digits at the back of your card.'])

<span class="btn btn-primary" id="card-button" data-secret="{{ $intent->client_secret }}">
    Continue <small class="fas fa-chevron-right"></small>
</span>
            
    
@push('footer-scripts')

    <script>
        /**
         * {{--
         * Create Stripe payment and attache the details as inputs to a form element.
         * On success, two inputs are attched to the form namely: payment_method and payment_method_provider=stripe. The form will be auto submitted unless if allowed.
         * 
         * @argument String paymentFormId The id of the form on which the payment method should be attached.
         * @argument Integer autoSubmit. SHould the payment form be automatically be submitted
         * --}}
         */
        function setStripePaymentMethod(paymentFormId,autoSubmit){
            
            //Custome elements
            const errorEle=$('#billing-payment-method-error');
            const successEle=$('#billing-payment-method-success');

            //Gather payment details
            const stripe = Stripe('{{config('laradmin.billing.stripe.key')}}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');

            // Verification
            const cardHolderName = document.getElementById('card-holder-name');

            const cardHolderEmail=document.getElementById('card-holder-email');
            const cardHolderPhone=document.getElementById('card-holder-phone');
            const cardHolderCity=document.getElementById('card-holder-city');
            const cardHolderCountry=document.getElementById('card-holder-country');
            const cardHolderLine1=document.getElementById('card-holder-line1');
            const cardHolderPostalCode=document.getElementById('card-holder-postal-code');

            const cardButton = document.getElementById('card-button');
            //const clientSecret = cardButton.dataset.secret;

            cardButton.addEventListener('click', async (e) => {
                errorEle.css({display:'none'});
                const { paymentMethod, error } = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details: { 
                            name: cardHolderName.value,
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
                );

                if (error) {
                    // Display "error.message" to the user...
                    
                    errorEle.text(error.message);
                    errorEle.css({display:'block'});
                    
                    
                } else {
                    // The card has been verified successfully...
                    
                    successEle.html('Saving ...');
                    successEle.css({display:'block'});

                    // $('#'+paymeny_method_input_id).val(setupIntent.payment_method);
                    // $('#'+payment_provider_input_id).val('stripe');

                    const paymentForm=$('#'+paymentFormId);

                    input1 = $('<input type="hidden" name="payment_method_provider" value="stripe">');
                    input2 = $('<input type="hidden" name="payment_method" value="'+paymentMethod.id+'">');

                    paymentForm.append(input1);
                    paymentForm.append(input2);


                    if(autoSubmit==1){
                        paymentForm.submit();
                    }

                    successEle.html('Verified ...');
                    
                }
            });
        }

        // Run the payment method
        setStripePaymentMethod('{{$payment_form_id}}',{{$auto_submit??1}})
    </script>
@endpush

