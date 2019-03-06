<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Lang;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\DB;
use BethelChika\Laradmin\UserGroup;
use Illuminate\Support\Facades\Gate;
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
        //var_dump(\Illuminate\Support\Facades\Auth::user());exit();
        $this->authorize('views','BethelChika\Laradmin\User');
        
        //$users=User::paginate(10);
        

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

        $countries=Lang::get('laradmin::list_of_countries');
        $faiths=Lang::get('laradmin::list_of_faiths');
        return view('laradmin::cp.user_create',compact('countries','faiths'));
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
            'first_names'=>'nullable|max:255|string',
            'last_name'=>'nullable|max:255|string',
            'year_of_birth'=>'nullable|integer|max:10000',
            'new_password' => 'required|string|min:6|confirmed|max:255',
            'gender'=>'nullable|string|max:10000',
            'faith'=>'nullable|string|max:255',
            'country'=>'nullable|string|max:255',
          ]);

           $user=new User;
           $user->email=$request->email;
           $user->name=$request->name;
           $user->first_names=$request->first_names;
           $user->last_name=$request->last_name;
           $user->year_of_birth=$request->year_of_birth;
           $user->gender=$request->gender;
           $user->faith=$request->faith;
           $user->country=$request->country;
 
           $user->password=Hash::make($request->new_password);
           $user->save();
           
           return redirect()->route('cp-user',$user->id)->with('success', 'User create successfully!');
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

        $countries=Lang::get('laradmin::list_of_countries');
        $faiths=Lang::get('laradmin::list_of_faiths');
        //var_dump($faiths);exit();
        return view('laradmin::cp.user_edit',compact('user','countries','faiths'));
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

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'first_names'=>'nullable|max:255|string',
            'last_name'=>'nullable|max:255|string',
            'year_of_birth'=>'nullable|integer|max:10000',
            'new_password' => 'nullable|string|min:6|confirmed|max:255',
            'gender'=>'nullable|string|max:10000',
            'faith'=>'nullable|string|max:255',
            'country'=>'nullable|string|max:255',
          ]);
         // regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/ |
          //update the user

          //$user=User::find($user->id);
          $user->name=$request->name;
          $user->first_names=$request->first_names;
          $user->last_name=$request->last_name;
          $user->year_of_birth=$request->year_of_birth;
          $user->gender=$request->gender;
          $user->faith=$request->faith;
          $user->country=$request->country;

          if(strcmp($request->new_password,''))$user->password=Hash::make($request->new_password);
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

        /* 
        $confirmed=DB::table('confirmation')->where('user_id','=',$user->id)->where('type','=','email_confirmation')->delete();

        $key= str_random(40);
        $now=\Carbon\Carbon::now();
        DB::table('confirmation')->insert(['key'=>$key,'type'=>'email_confirmation','user_id'=>$user->id,'created_at'=>$now,'updated_at'=>$now]);
        $confirmationLink= route('email-confirmation',[$user->email,$key]);

        \Illuminate\Support\Facades\Mail::to($user->email)
        ->send(new \App\Mail\Laradmin\UserConfirmation($user,$confirmationLink)); */
        
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

        // DB::table('confirmation')
        // ->where('user_id','=',$user->id)
        // ->where('type','=','email_confirmation')
        // ->delete();//delete confirmation 

        // $user->status=1;
        // $user->save();
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

