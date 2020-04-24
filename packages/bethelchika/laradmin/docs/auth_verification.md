# Auth verification
Laradmin provides verification system that allows you to verify a LoginAttempt model. The Login attempt model is saved when a user logs in or following a successful verification.

How often a request is examined for verification depends on the *laradmin.login_attempt_check* config. depending the config, when an authenticated user makes a request, LoginAttempt information is extracted from the request. The extracted information is checked against the previous LoginAttempt by the user. The user is forced to undergo a verification process if there is no previous verified matching  attempt.

It is possible to change how tight the requirements for matching a previous attempt with a current attempt, by changing the columns of the LoginAttempt model table that are used in the comparison. For example removing the *ip* column could reduce the number of times a user is asked to verify their account if the user is using an internet connect that constantly varies the WAN IP. See the config *laradmin.login_attempt_match_columns* for more details.

## Verification Channels
Verification process are completed by verification channels. Out of the box, Laradmin provides password, email and security question channels. A channel class should implement the BethelChika\Laradmin\AuthVerification\Contracts\Channel interface.

## Processing
A channel will be presented during a verification with security level less than or equal to the channel's max_level.

If a user chooses a channel for verification then processing will be transferred to the channel by making a get request to the channels homepage. The homepage path for a channel must follow thus:

```
/u/auth-verification/channel/$tag
```
where $tag is the channels tag. So channels should create a route and a controller to handle the request in this path. A channel is responsible for creating other routes that allows it to complete the verification process.

A typical channel controller method using the Email Channel as an example
```php
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;
use BethelChika\Laradmin\AuthVerification\Channels\Email;
use BethelChika\Laradmin\LoginAttempt;
public function email(Request $request)
        
        // Get the current attempt information
        $attempt=LoginAttempt::getCurrentAttempt($request); 
        
        //Initialise the channel
        $email_channel=new Email; 

        //It is a good idea to redirect the user if actually no verification is required
        if(!AuthVerificationManager::has2Verify($attempt)){
           return $this->intended();
        }

        // It is a good practice (not not required) to redirect the user if infact this channel cannot hand the current verification. The verification will fail later anyway if you carry on.
        if(!$attempt->canVerify($email_channel)){
            return redirect()->route('user-auth-v');
        }

        ...

        //Verify after the channels has conducted the verification process.
        $re=$email_channel->verify($attempt,$request->code);//Where $request->code is a code a user retrieved from her email.


        if($re===null){
            return redirect()->route('user-auth-v')->with('danger','Verification failed');
        }

        if(!$re){
            return redirect()->route('user-auth-v-email-step3',[$request->email_id])->with('danger','Invalid details');
             
        }   

        $pageTitle='Verification is complete';
        return view('laradmin::user.auth_verification.done',compact('pageTitle'));
```


A channel views can extend auth verification sub-layout view in order to keep the look and feel of verification process consistent. 
```
@extend('laradmin::user.auth_verification.sub_layouts.index')
```
Since this is a sub-layout, contents of the extending/child views should not include container class as this provided.

A channel may return the view view('laradmin::user.auth_verification.done') after a successful verification.

### Custom channels
Currently there no example provided, but it is possible to dynamically register a channel. This should be done is register method of a service provider thus:
```php
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;

AuthVerificationManager::registerChannel($tag,$max_level,$class);
...
```
Where $tag is a unique tag of the channel, $max_level is an integer representing the maximum security level that the channel can handle. And $class is the fully qualified classname of the Channel class.

Once registered custom channels are like any other channel.

## Examples
Use the implement password channel as very simple example as this channel is quite straight forward.

## For reverification.
If for some reason you feel that a LoginAttempt should reverify, you should call the mustReverify method of the LoginAttemp thus:
```php
$attempt->mustReverify($level);
```
Where $level is the security level at which you want the verification to be done under. If you want the user to very all previous attempts, you should call the static LoginAttempt::mustReverifyAll thus:
```php
use BethelChika\Laradmin\LoginAttempt;

LoginAttempt::mustReverifyall($user);
```