<?php

namespace BethelChika\Comicpic\Http\Controllers;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Laradmin;
use BethelChika\Comicpic\Http\Requests\FileUpload;
use BethelChika\Comicpic\Http\Requests\FileDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use BethelChika\Comicpic\Models\Comicpic;
use BethelChika\Comicpic\Feed\Feed;
use BethelChika\Comicpic\Http\Controllers\Traits\Helper;


class UserController extends Controller
{
    use Helper;
    public $mediaManager;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        $this->middleware('auth');
        $this->mediaManager=$laradmin->mediaManager;
        parent::__construct($laradmin);
        
    }



     /**
     * Show the application settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        $user=Auth::user();

        $this->authorize('view',$user);
        
        $pageTitle='Settings';
        return view('comicpic::setting',compact('pageTitle'));
    }
    public function create(){
        $user=Auth::user();
        
        $this->authorize('update',$user);
        $pageTitle='Upload';
        return view('comicpic::create_edit',compact('pageTitle'));

    }
        
    public function store(FileUpload $request){
        $user=Auth::user();
        $this->authorize('update',$user);
       
        $media=$this->mediaManager->fromSource($request->file('file'),'comicpic',null,'public','public',$user);

        $bodyClasses='comicpic';
        if($media and $this->mediaManager->exists($media)){
            $comicpic=Comicpic::createWithMedia($media,$user);
            
            // Send straight to edit so that things like title can be defined

            if($request->acceptsJson()){
                return ['redirectTo'=>route('comicpic.edit',$comicpic->id),'msg'=>'Done'];
            }
            return redirect()->route('comicpic.edit',$comicpic->id);
        
        }else{
            $error_msg='There was error with the upload. Please try again.';
            if($request->acceptsJson()){
                return response()->json(['error'=>$error_msg],400);
            }
            return back()->with('danger',$error_msg);
        }

    }

    public function edit(Request $request){
        
        $user=Auth::user();
        $this->authorize('update',$user);

        $comicpic=Comicpic::find($request->comicpic_id);
        $media=null;
        if($comicpic){
            $media=$comicpic->medias()->where('tag','comicpic')->first();    
        }
        if(!$media or !($media->user_id==$user->id)){
            return redirect()->route('comicpic.create')->with('danger','There was error with the upload. Please try again');
        }

        $pageTitle='Upload details';
        return view('comicpic::create_edit',compact('pageTitle','comicpic','media'));
    }

    public function update(FileDetails $request){
        $user=Auth::user();
        $this->authorize('update',$user);
 
        $comicpic=Comicpic::find($request->comicpic_id);
        
        $media=null;
        if($comicpic){
            $media=$comicpic->medias()->where('tag','comicpic')->first();    
        }
        if(!$media or !($comicpic->user_id==$user->id) or !($media->user_id==$user->id)){//FIXME: Part of this condition blocks using image uploaded by another person, is this fine?
            return redirect()->route('comicpic.create_edit')->with('danger','There was error with the upload. Please try again');
        }
    
        //Use a trick here to find if we are updating for the first time, which means this is a new item
        $isnew=false;
        if($comicpic->title==null and $comicpic->description==null){
            $isnew=true;
        }
        
        $comicpic->title=$request->title;
        $comicpic->description=$request->description;
        $comicpic->hashtags=$request->hashtags;
        $comicpic->twitter_via=$request->twitter_via;
        $comicpic->twitter_screen_names=$request->twitter_screen_names;
        
        if($comicpic->update()){
            if($isnew){
                //lets post a feed since its a new item
                if($comicpic->published_at){
                    Feed::post($comicpic);
                }
            }
            return back()->with('success','Saved');
        }else{
            return back()->with('danger','There was error with the saving the details. Please try again.');
        }

        
    }

    public function destroy(Comicpic $comicpic){
        $user=Auth::user();
        $this->authorize('delete',$user);
        return $this->destroyer($comicpic);
        // if($comicpic->delete()){
        //     if($comicpic->published_at or $comicpic->published_at){
        //         if(Feed::delete($comicpic) ){

        //         }else{
        //             Log::warning('The feed with source_id='.$comicpic->id.' and source_type='. get_class($comicpic).' class not deleted when when associated comicpic was deleted. Delete it manually');
        //             return back()->with('warning','Done with some issues. The associated feed may not have been deleted. Please delete it manually');
        //         }
        //     }
        //     return back()->with('success','Done');
            
        // }else{
        //     return back()->with('danger','There was error with the delete action. Please try again.');
        // }

    }

    public function publish(Comicpic $comicpic){
        if(!$comicpic->published_at){
            if($comicpic->publish()){
                if(!$comicpic->unpublished_at){//If it has been previously unpublished then it should have been published in the past at what time it should have been posted; so do not post again
                    Feed::post($comicpic);
                }
                return redirect()->route('comicpic.index')->with('success','Done');
            }else{
                return back()->with('danger','There was error with the publishing. Please try again.');
            }
        }else{
            return back()->with('warning','This item is already published');
        }
    }

    public function unpublish(Comicpic $comicpic){
        if($comicpic->published_at){
            if($comicpic->unpublish()){
                return back()->with('success','Done');
            }else{
                return back()->with('danger','There was error with the unpublishing. Please try again.');
            }
        }else{
            return back()->with('warning','This item is not published');
        }
    }

    
         /**
     * Show the application lists of items for the currently loggeed in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        $user=Auth::user();
        $this->authorize('view',$user);
        //\Illuminate\Support\Facades\DB::enableQueryLog();
        //
        $comicpics=ComicPic::has('medias')->with(['medias'=>function($query){
            $query->where('tag', 'comicpic');
        },'user'])
        ->where('user_id',$user->id)
        ->latest('published_at')->paginate(48);
        //$comicpics=ComicPic::with('medias')->get();
        //dd(\Illuminate\Support\Facades\DB::getQueryLog());
        
        //dd($comicpics);
        $pageTitle='My Comicpic';
    
        return view('comicpic::me',compact('pageTitle','comicpics'));
    }
    
   
}
