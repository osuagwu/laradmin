<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Lang;
use BethelChika\Laradmin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\Traits\EmailConfirmationEmail;
use BethelChika\Laradmin\Http\Controllers\User\Traits\ReAuthController;
use BethelChika\Laradmin\Laradmin;
use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\Form\Form;
use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\SecurityQuestion;
use Illuminate\Support\Facades\Validator;
use  BethelChika\Laradmin\Http\Controllers\User\Traits\UserAvatar;
use BethelChika\Laradmin\Http\Controllers\User\Traits\UserCoverPhoto;
use BethelChika\Laradmin\Media\Models\Media;

class UserProfileController extends Controller
{
    use EmailConfirmationEmail, ReAuthController,UserAvatar,UserCoverPhoto;

    private $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['emailConfirmation','privacy']]);
        
        $this->middleware('re-auth:30')->only(['edit',//main methods
                                                'avatar','avatarJson','avatarJsonStore','avatarJsonUpdate','avatarJsonDelete',//avatar methods
                                                'coverPhoto','coverPhotoJson','coverPhotoJsonStore','coverPhotoJsonUpdate','coverPhotoJsonDelete',//cover photo methods
                                                ]);

        $this->middleware('re-auth:5')->only(['editPassword' ,'updatePassword','securityQuestionsUpdate','securityQuestionsEdit', 'initiateSelfDelete', 'selfDeactivate']); //Set a much more strict rerauth params for changing password.
        
        // Set sub app name
        $laradmin->contentManager->registerSubAppName('User manager',route('user-profile'));

        $this->laradmin = $laradmin;
        // Load menu items for user settings
        $laradmin->contentManager->loadMenu('user_settings');

        //Register classes
        $laradmin->assetManager->registerBodyClass('sidebar-white');
        //Remove main nav border-bottom
        $laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
    }

    /**
      * Show the user home.
      *
      * @return \Illuminate\Http\Response
      */
    public function index(Laradmin $laradmin)
    {
        $pageTitle = "Home";
        
        $laradmin->assetManager->registerMainNavScheme('primary');

        //Get blog posts
        $posts=collect();
        if(config('laradmin.wp_enable',false)){
            $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->get();
        }

        return view('laradmin::user.index', compact('pageTitle', 'posts', 'laradmin'));
    }

   

    /**
      * Show the profile info.
      * @param string $form_pack A form pack which actually should always be 'user_settings'.
      * @param string $form_tag A tag of a form in the 'user_settings' pack
      * @return \Illuminate\Http\Response
      */
    public function profile(Request $request,$form_pack = null, $form_tag = null)
    {
        $user = $request->user();
        $this->authorize('view', $user);

        if (!$form_pack or !$form_tag) {
            return redirect()->route('user-profile', ['user_settings', 'personal']);
        }


        $pageTitle = 'Welcome ' . $user->name;

        $this->laradmin->assetManager->registerMainNavScheme('primary');
        $this->laradmin->assetManager->registerBodyClass('user-profile-page');
        $this->laradmin->assetManager->setContainerType('fluid');



        // Get form for profile
        $form = new Form($form_pack, $form_tag,'index');
        $form->editLink=route('user-profile-edit',[$form_pack, $form_tag]);
        $forms_nav_tag = $form->packToMenu(route('user-profile'),'user_settings.account');


        // See if we can add a cover photo
        $media_cover_photo=$user->medias()->where('tag','cover-photo')->first();
        if($media_cover_photo){
            $sm_url=$media_cover_photo->url('_cover_photo_sm_');
            // If we still do not have the small one we won't do anything. 
            // This is because we are not checking if the request is from 
            // a mobile or not and don't want the mobile to download large 
            // image. TODO: fix by checking if is desktop or small device 
            // and serve the correct bg instead of all these responsive css
            // we are creating below,???.
            if($sm_url){
                $this->showCoverPhoto($media_cover_photo->url(),$sm_url);
            }else{
                $media_cover_photo=null;
            }   
        }
        


        $show_profile_card = false; //When true similar profile card shown in the dashboard will be shown in the profile page too

        return view('laradmin::user.profile', ['pageTitle' => $pageTitle, 'user'=>$user,'media_cover_photo'=>$media_cover_photo, 'show_profile_card' => $show_profile_card, 'form' => $form, 'forms_nav_tag' => $forms_nav_tag]);
    }

    /**
     * Place a cover photo on the page 
     *
     * @param string $img_url_lg url of large version of image
     * @param string $img_url_sm Url of small version/mobile devices' version of image
     * @return void
     */
    private function showCoverPhoto($img_url_lg,$img_url_sm){
        $img_urls=[
            'lg'=>$img_url_lg,
            'sm'=>$img_url_sm,
        ];

        $css=$this->laradmin->assetManager->makeResponsiveCssBg($img_urls,'.cover-photo');
        $this->laradmin->assetManager->registerAsset('head-styles','mainbar-cover',$css);
        $this->laradmin->assetManager->registerBodyClass('has-cover-photo');
       
    }



    /**
      * Show main settings.
      *
      * @return \Illuminate\Http\Response
      */
    public function settings(Laradmin $laradmin)
    {
        $user = Auth::user();
        $this->authorize('view', $user);
        $pageTitle = 'Welcome, ' . $user->name;
        $this->laradmin->assetManager->setContainerType('fluid');
        $laradmin->assetManager->unregisterBodyClass('main-nav-no-border-bottom');
        return view('laradmin::user.settings', compact('pageTitle'));
    }



    /**
      * Show the edit form.
      *
      * @return \Illuminate\Http\Response
      */
    public function edit($form_pack, $form_tag)
    {
        $this->authorize('update', Auth::user()); //No need to authorize here but we will do it anyways

        // Get form for profile
        $form = new Form($form_pack, $form_tag,'edit');
        $form->link=route('user-profile',[$form_pack, $form_tag]);
        $form->title='';


        $pageTitle = 'Edit profile | ' . Auth::user()->name;
        return view('laradmin::user.edit', ['user' => Auth::user(), 'pageTitle' => $pageTitle, 'form' => $form]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $form_pack, $form_tag)
    {

        $user = $request->user();

        $this->authorize('update', $user); 

        //$save_profile=false;

        // Get form for profile
        $form = new Form($form_pack, $form_tag,'edit');


        // First process intrinsic 
        switch ($form->getTag()) {
            case 'personal':
                $rules = [
                    'name' => 'required|string|max:255',
                ];
                $this->validate($request, $rules);
                $user->name = $request->name;
                //$save_profile=true;
                $user->save();
                break;
            case 'contacts':
                break;
            default:
        }

        // Now process extrinsic fields
        $form->getValidator($request->all())->validate();
        $form->process($request);


        // if($save_profile){
        //     $user->save();
        // }

        

        return redirect()->route('user-profile', [$form->getPack(), $form->getTag()])->with('success', 'Done!');
    }


    


    /**
       * Display security view
       *
       *  @return \Illuminate\Http\Response
       */
    public function security()
    {
        $user=Auth::user();
        $this->authorize('update', $user);
        $this->laradmin->assetManager->setContainerType('fluid');


        $pageTitle = "Security";
        return view('laradmin::user.security', compact('pageTitle'));
    }


       /**
       * Display login attempts view
       *
       *  @return \Illuminate\Http\Response
       */
      public function loginAttempts()
      {
          $user=Auth::user();
          $this->authorize('update', $user);
          $this->laradmin->assetManager->setContainerType('fluid');
  
         
   
            
          $login_attempts=$user->loginAttempts()->latest('updated_at')->get();
          $pageTitle = "Security - Login attempts";
          return view('laradmin::user.security_login_attempts', compact('pageTitle','login_attempts'));
      }

      /**
       * Logout other devices leaving the current one.
       * See https://laravel.com/docs/7.x/authentication#events for "Invalidating Sessions On 
       * Other Devices" to open the add a required middleware i.e \Illuminate\Session\Middleware\AuthenticateSession::class
       *
       * @param Request $request
       * @return \Illuminate\Http\Response
       */
      public function logoutOtherDevices(Request $request){
          

          $this->validate($request,[
              'password'=>'required|string'
          ]);
          
          $user=$request->user();
          
          $password=$request->input('password');

          if(!Hash::check($password,$user->password)){
            return back()->with('danger','Incorrect password');
          }

          
            Auth::logoutOtherDevices($password);

            return back()->with('success','Done');
      }

      /**
       * Deletes login a attempt 
       * @param $attempt_id The id of the attempt
       * @return \Illuminate\Http\Response
       */
      public function loginAttemptDestroy(Request $request,$attempt_id)
      {
          $user=$request->user();
          $this->authorize('update', $user);   
            
          $user->loginAttempts()->where('id',$attempt_id)->first()->delete();

          return back()->with('success','Done');
      }


          
        /**
       * Display security questions view
       *
       *  @return \Illuminate\Http\Response
       */
      public function securityQuestions()
      {
          $user=Auth::user();
          $this->authorize('update', $user);
          $this->laradmin->assetManager->setContainerType('fluid');
  
  
          $security_answers=$user->securityAnswers;
          $security_answers_count=config('laradmin.security_answers_count');

  
  
          $pageTitle = "Security - Security questions";
          return view('laradmin::user.security_questions', compact('pageTitle','security_answers','security_answers_count'));
      }

    /**
     * Display security questions view
     *
     *  @return \Illuminate\Http\Response
     */
    public function securityQuestionsEdit(){
        $user=Auth::user();
        $this->authorize('update', $user);
        $this->laradmin->assetManager->setContainerType('fluid');

        $security_answers=$user->securityAnswers;
        
        $security_answers_count=config('laradmin.security_answers_count');// The require number of security questions


        // Turn the questions into options
        $questions_options=[];
        foreach(SecurityQuestion::all() as $sq){
            $questions_options[$sq->id]=$sq->question;
        }



        // Turn the answers into key:value where the question id is the 'key' and the 'value' is an array such that [answer,reminder].
        $answers_values=[];
        if($security_answers->count()==$security_answers_count){
            foreach($security_answers as $sa){
                $answers_values[$sa->securityQuestion->id]=['answer'=>$sa->answer,'reminder'=>$sa->reminder];
            }
        }


        $pageTitle = "Security questions";
        return view('laradmin::user.security_questions_edit', compact('pageTitle','questions_options','answers_values','security_answers_count'));
    }

    /**
     * Update security questions 
     *  @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function securityQuestionsUpdate(Request $request){
        $user=Auth::user();
        $this->authorize('update', $user);
        $this->laradmin->assetManager->setContainerType('fluid');
        
        $security_answers_count=config('laradmin.security_answers_count');
        
        

        

        // Make sure appropriate number of unique answers are provided.
        $security_question_keys=\array_unique($request->security_questions);
        if(!is_array($security_question_keys) or count($security_question_keys)!==$security_answers_count){
            $request->flash();
            return back()->with('warning','Please answer '. $security_answers_count.' unique questions');
        }

        // Turn the questions into array
        $security_questions=[];
        foreach(SecurityQuestion::all() as $sq){
           $security_questions[$sq->id]=$sq->question;
        }

        //Make sure actual questions are selected
        foreach($request->security_questions as $sq_key){
            if(!array_key_exists($sq_key,$security_questions)){
                $request->flash();
                return back()->with('warning','Please answer valid questions');
            }
        }

        $security_answers=$request->security_answers;

        
        // Break all the reminders into comma separated words. And merge the comma separated words from all reminders. 
        for ($i=0;$i<$security_answers_count;$i++) {
            $reminder_pieces[]=preg_replace('/\s+/', ',', $request->security_answer_reminders[$i]);//replace white spaces with a single comma
        }
        $reminder_pieces=implode(',',$reminder_pieces);

        
        //TODO: validation to prevent having 'none common' words that are present in both anwers and reminders.

        
        // Create a separate validation to check that answers do not exist in in any of the reminders
        $validator= Validator::make($request->only('security_answers'),[
            'security_answers.*'=>'not_in:'.$reminder_pieces,
            ],
            [
                'security_answers.*.not_in'=>'An answer should not be contained in an reminder',         
            ]
        )->validate();


        // Main validation
        $validator= Validator::make($request->all(),[
                'security_questions' => 'required|array|max:'.$security_answers_count,
                'security_questions.*'=>'required|numeric|max:1000',
                'security_answers'=>'required|array|max:'.$security_answers_count,
                'security_answers.*'=>'required|string|max:250',
                'security_answer_reminders'=>'nullable|array|max:'.$security_answers_count,
                'security_answer_reminders.*'=>'nullable|string|max:250',
            ],
            [
                'security_questions.*.max'=>'We may not know the ecurity question',
                'security_questions.*.required'=>'Security question is required',
                'security_questions.*.numeric'=>'Security format is unknown',
                'security_answers.*.max'=>'Security answer must not exceed 250 characters',
                'security_answers.*.required'=>'Security answer is required',
                'security_answers.*.string'=>'Security answer must be a string',
                'security_answer_reminders.*.string'=>'Security answer reminder must be a string',
                'security_answer_reminders.*.max'=>'Security answer reminder not exceed 250 characters',
                        
            ]
        )->validate();

        $user->securityAnswers()->delete();
        for($i=0;$i<count($request->security_questions);$i++){
                        
            $remind=$request->security_answer_reminders[$i];
            
            // And now save
            $user->securityAnswers()->create(['security_question_id'=>$request->security_questions[$i],'answer'=>$security_answers[$i],'reminder'=>$remind]);
        }

        return redirect()->route('user-security-questions')->with('success','Done');

    }

    /**
      * Show the edit form for password.
      *
      * @return \Illuminate\Http\Response
      */
    public function editPassword()
    {
        $this->authorize('update', Auth::user()); //No need to authorize here but we will do it anyways


        $pageTitle = 'Edit password | ' . Auth::user()->name;
        return view('laradmin::user.edit_password', ['user' => Auth::user(), 'pageTitle' => $pageTitle]);
    }

    /**
     * Update the password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {

        $this->authorize('update', Auth::user());


        $this->validate($request, [
            'new_password' =>config('laradmin.rules.password')

        ]);
        
        //update the user
        $user = User::find(Auth::user()->id);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user-security')->with('success', 'Password updated!');
    }

    /**
     * Send email confirmation email to the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    function sendEmailConfirmation($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $this->authorize('update', $user);

        if ($user->isEmailConfirmed()) {
            return redirect()->route('user-home')->with('warning', 'Email is already confirmed');
        }


        $sentEmail = $this->confirmEmailEmail($user);

        return view('laradmin::user.email_confirmation', compact('sentEmail'));
    }

    /**
     * Confirm the email of a user who is identified my the specified email
     *
     * @param  string $email
     * @param  string $token
     * @return \Illuminate\Http\Response
     */
    function emailConfirmation($email, $token)
    {
        $user = User::where('email', '=', $email)->get();
        if ($user->count() == 1) {
            $user = $user->first();
        } else {
            $user = false;
        }
        

        $confirmed = 0;
        $row = false;

        if ($user) {
            if ($user->isEmailConfirmed()) {
                return redirect()->route('user-home')->with('warning', 'Email is already confirmed');
            }
            $row = DB::table('confirmations')->where('user_id', '=', $user->id)->where('type', '=', 'email_confirmation')->first();
        }

        if ($row) {
            $now = \Carbon\Carbon::now();
            
            $expired = ($now > \Carbon\Carbon::parse($row->created_at)->addHours(1));
            
            if (!strcmp($token, $row->token) and !$expired) {
                $user->status = 1;
                $user->save();

                $confirmed = 1;
            }

            if ($confirmed or $expired) {
                DB::table('confirmations')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', 'email_confirmation')
                    ->delete(); //delete confirmation
            }
        }
        return view('laradmin::user.email_confirmation', compact('confirmed'));
    }




    /**
     * SHows account control settings
     *
     * @return \Illuminate\Http\Response
     */
    public function accountControl()
    {
        $user = Auth::user();
        $this->authorize('delete', $user);

        $pageTitle = 'Account control';
        return view('laradmin::user.account_control', compact('pageTitle'));
    }


    /**
     * Initiate self deletion of user account
     *
    * @param  void
    * @return \Illuminate\Http\Response
    */
    function initiateSelfDelete()
    {
        $user = Auth::user();
        $this->authorize('delete', $user);

        //Check that the required action is not done already
        if ($user->self_delete_initiated_at) {
            return back()->with('warning', 'No action required');
        }

        $lastChanceDate = $user->initiateSelfDelete();
        if ($lastChanceDate) {
            $user->getSystemUser()->notify(new Notice('Self deleted (user id:' . $user->id . ')'));
            Auth::logout();
            return redirect()->route('login')->with('success', 'Your account was marked for deletion. It will be deleted approximately on ' . $lastChanceDate->toFormattedDateString() . '. You can recover it before this date; just sign in and cancel the permanent deletion from your settings.');
        } else {
            $user->getSystemUser()->notify(new Notice('Self deletion attempt failed (user id:' . $user->id . ')'));
            return back()->with('danger', 'Unable to delete your account. Please contact administrator');
        }
    }
    /**
     * Cancel self deletion of user account
     *
    * @param  void
    * @return \Illuminate\Http\Response
    */
    function cancelSelfDelete()
    {
        $user = Auth::user();
        $this->authorize('delete', $user);

        //Check that the required action is not done already
        if ($user->self_delete_initiated_at == null) {
            return back()->with('warning', 'No action required');
        }

        $isDone = $user->cancelSelfDelete();
        if ($isDone) {
            $user->getSystemUser()->notify(new Notice('Self deletion cancelled (user id:' . $user->id . ')'));
            $user->notify(new Notice('Self deletion on your account was cancelled'));
            return back()->with('success', 'Your account is restored');
        } else {
            $user->getSystemUser()->notify(new Notice('Self deletion cancellation failed (user id:' . $user->id . ')'));
            return back()->with('danger', 'Unable to cancel account deletion. Please contact administrator');
        }
    }

    /**
    * Self Deactivate account
    *
    * @param  void
    * @return \Illuminate\Http\Response
    */
    function selfDeactivate()
    {
        $user = Auth::user();
        $this->authorize('update', $user);

        //Check that the required action is not done already
        if ($user->self_deactivated_at) {
            return back()->with('warning', 'No action required');
        }

        $isDone = $user->selfDeactivate();

        if ($isDone) {

            $user->getSystemUser()->notify(new Notice('Self deactivation  (user id:' . $user->id . ')'));
            Auth::logout();
            return redirect()->route('login')->with('success', 'Your account is deactivated. To reactivate, simply sign back in.');
        } else {
            $user->getSystemUser()->notify(new Notice('Self deactivation attempt failed (user id:' . $user->id . ')'));
            return back()->with('danger', 'Unable to deactivate. Please contact administrator');
        }
    }
    /**
     * Self Reactivate account. If auto reactivation following login ever fails, this function allows a user to self reactivate from their settings page
     *
     *
    * @param  void
    * @return \Illuminate\Http\Response
    */
    function selfReactivate()
    {
        $user = Auth::user();
        $this->authorize('update', $user);



        $isDone = $user->selfReactivate();

        if ($isDone == 1) {
            $user->getSystemUser()->notify(new Notice('Self reactivation  (user id:' . $user->id . ')'));
            $user->notify(new Notice('Your account was reactivated from self-deactivation'));
            return back()->with('success', 'Your account is reactivated');
        } elseif ($isDone == 2) {
            return back()->with('warning', 'No action required');
        } else {
            $user->getSystemUser()->notify(new Notice('Self reactivation attempt failed (user id:' . $user->id . ')'));
            return back()->with('danger', 'Unable to reactivate. Please contact administrator');
        }
    }

    /**
    * Shows the alerts for a user
    *
    *
    * @param  void
    * @return \Illuminate\Http\Response
    */
    function userAlerts()
    {
        $user = Auth::user();
        $this->authorize('view', $user);
        $alerts = $user->getAlerts();
        $pageTitle = 'Alerts';

        return view('laradmin::user.alerts', compact('pageTitle', 'alerts'));
    }



    /**
     * Display site privacy information
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        $pageTitle = 'Privacy policy';
        $page=null;
        $privacy_content=null;
        if(config('laradmin.wp_enable') and config('laradmin.wp_use_privacy')){
            $privacy_content=Page::getPageParts(['privacy']);
            if(!$privacy_content){
                $page=Page::where('post_name','privacy-policy')->first();
            }
        }
        return view('laradmin::user.privacy', compact('privacy_content','page','pageTitle'));
    }
}
