@if(config('laradmin.cookie_consent.enable') && !Cookie::has(config('laradmin.cookie_consent.name')))
    <div class="js-cookie-consent cookie-consent">



        <span class="cookie-consent-message">

            {!! trans('laradmin::cookie_consent.message') !!}
             <a href="{{route('user-privacy')}}">Privacy policy</a>.

        </span>



        <button class="js-cookie-consent-agree cookie-consent-agree">

            {{ trans('laradmin::cookie_consent.agree') }}

        </button>



    </div>
    @push('footer-scripts')
        <script>
            window.cookieConsent = (function () {
                const COOKIE_VALUE = 1;
                const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';
    
                function consentWithCookies() {
                    setCookie('{{config('laradmin.cookie_consent.name')}}', COOKIE_VALUE, {{config('laradmin.cookie_consent.lifetime')}});
                    hideCookieDialog();
                }
    
                function cookieExists(name) {
                    return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
                }
    
                function hideCookieDialog() {
                    const dialogs = document.getElementsByClassName('js-cookie-consent');
                    for (let i = 0; i < dialogs.length; ++i) {
                        dialogs[i].style.display = 'none';
                    }
                }
    
                function setCookie(name, value, expirationInDays) {
                    const date = new Date();
                    date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                    document.cookie = name + '=' + value
                        + ';expires=' + date.toUTCString()
                        + ';domain=' + COOKIE_DOMAIN
                        + ';path=/{{ config('session.secure') ? ';secure' : null }}';
                }
    
                if (cookieExists('{{ config('laradmin.cookie_consent.name') }}')) {
                    hideCookieDialog();
                }
    
                const buttons = document.getElementsByClassName('js-cookie-consent-agree');
                for (let i = 0; i < buttons.length; ++i) {
                    buttons[i].addEventListener('click', consentWithCookies);        
                }

                // $(document).on('vue-loaded',function(){//Vue is likely to remove the above event but we will re-attach it after vue is laoded, with a little help from jquery as Vue dispatches a jquery event
                //     const buttons = document.getElementsByClassName('js-cookie-consent-agree');
                //     for (let i = 0; i < buttons.length; ++i) {
                //         buttons[i].addEventListener('click', consentWithCookies);
                //     }
                // });
                
                return {
                    consentWithCookies: consentWithCookies,
                    hideCookieDialog: hideCookieDialog
                };
                
            })();
        </script>
    @endpush
@endif