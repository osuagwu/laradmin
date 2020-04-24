<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;


use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\Traits\EmailConfirmationEmail;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class UserController extends Controller
{
    use EmailConfirmationEmail;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
         $this->middleware('auth');
         parent::__construct();

     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cpAuthorize();

        $this->authorize('views','BethelChika\Laradmin\User');


        $request=Request();

        $order_by=$request->get('order_by','id');
        $order_by_dir=$request->get('order_by_dir','asc');
        $currentOrder=$order_by.':'.$order_by_dir;

        if($request->search){
            $search_str='%'.$request->get('users_search').'%';
            $users=User::where('name','like',$search_str)->orderBy($order_by,$order_by_dir)->paginate(10);
            $request->flash('users_search');
        }
        else{

            $users=User::orderBy($order_by,$order_by_dir)->paginate(10);
        }
        return view('laradmin::cp.users',['users'=>$users,'currentOrder'=>$currentOrder]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
        $this->authorize('create','BethelChika\Laradmin\User');

        $pageTitle="New user";
        return view('laradmin::cp.user_create',compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->cpAuthorize();
        $this->authorize('create','BethelChika\Laradmin\User');

        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string|max:255',
            'password' =>config('laradmin.rules.password'),

        ]);

           $user=new User;
           $user->email=$request->email;
           $user->name=$request->name;


           $user->password=Hash::make($request->new_password);
           $user->save();

           return redirect()->route('cp-user',$user->id)->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        $this->cpAuthorize();

        $this->authorize('view',$user);

        $user_groups=[];
        foreach ($user->userGroupMap as $ugm){

            $user_groups[]=UserGroup::find($ugm->user_group_id);
        }
        //exit(var_dump($user_groups[0]->name));
        return view('laradmin::cp.user',compact('user','user_groups'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->cpAuthorize();

        $this->authorize('update',$user);

        return view('laradmin::cp.user_edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->cpAuthorize();

        $this->authorize('update',$user);




        $rules=[
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules); 
         

          $user->name=$request->name;


          if(strcmp($user->email,$request->email)){
                //validate emails
                $this->validate($request, [
                    'email' => 'required|string|email|max:255|unique:users',
                ]);

                $user->email=$request->email;
            }

           // dd(trans('laradmin::messages.password'));
          if (strcmp($request->password, '')) {

            // Validate for password
            $this->validate($request, [
                'password' =>config('laradmin.rules.password'),
            ]);

            $user->password=Hash::make($request->password);
          }


          $user->save();

          return redirect()->route('cp-user',$user->id)->with('success', 'User updated!');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->cpAuthorize();

        $this->authorize('delete',$user);

        foreach ($user->userGroupMap as $userGroupMap){
            //NOTE:: open the next line if you want to delete the groups as well
            $userGroupMap->delete();
        }
        $user->hardDelete();
        return back()->with('success', 'User sucessfully deleted');
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function destroys(Request $request)
     {
        $this->cpAuthorize();

         $i=0;
         foreach (explode(',',$request->users_ids) as $user_id){
            $user=User::find($user_id);

            $this->authorize('delete', $user);

            //If the login user is authorised to delete the user then the user should be allowed to delete the user_group mappings
            if(!$user)continue;
            foreach ($user->userGroupMap as $userGroupMap){
                //NOTE:: open the next line if you want to delete the groups as well
                $userGroupMap->delete();//NOTE that databse level constrain may have taken care of this.
            }
            $i++;
            $user->hardDelete();
        }
         if($i)
         return back()->with('success', $i.' user(s) sucessfully deleted');
         else
         return back()->with('info', 'Nothing was deleted');
     }

    /**
     * Send email confirmation email to the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
     function sendEmailConfirmation(User $user){
        $this->cpAuthorize();

        $this->authorize('update',$user);//We just authorise this as an update

     

        $sentEmail=$this->confirmEmailEmail($user);

        return back()->with('success','Confirmation email has been sent to '. $user->email.'.');
     }

     /**
     * Accept the email of the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
     function emailConfirmation(User $user){
        $this->cpAuthorize();
        $this->authorize('update',$user);


        if($user->confirmEmail()){
            return back()->with('success','Email ('. $user->email.') was marked as confirmed');
        }else{
            return back()->with('danger','There was a problem accepting Email ('. $user->email.') as confirmed');
        }

     }







     /**
     * Disable the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
     function disableUser(User $user){
        $this->cpAuthorize();
        $this->authorize('update',$user);

        if($user->is_active==0){
            return back()->with('warning','No change required');
        }
        $user->is_active=0;
        $user->save();
        $user->notify(new Notice('Your account is disabled.'));
        return back()->with('success','User was updated');
     }

     /**
     * Enable the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
     function enableUser(User $user){
        $this->cpAuthorize();

        $this->authorize('update',$user);
        if($user->is_active==1){
            return back()->with('warning','No change required');
        }
        $user->is_active=1;
        $user->save();
        $user->notify(new Notice('Your account is enabled.'));
        return back()->with('success','User was updated');
     }
}

