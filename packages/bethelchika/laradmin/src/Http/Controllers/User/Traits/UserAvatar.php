<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits;
use \Illuminate\Http\Request;
/**
 * Adds capability to handle profile avatar
 */
trait UserAvatar
{
    /**
     * The main enterance to editing an avatar
     * 
    * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function avatar(Request $request)
    {
        $user=$request->user();
        $this->authorize('update', $user);

        $pageTitle='Avatar';
        return view('laradmin::user.avatar.index',compact(['pageTitle','user']));
    }

    /**
     * Saves new or replaces current avatar
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function avatarJsonStore(Request $request)
    {
        //return $request->file('file')[0].'---'.$request->file('file')[1];
        
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'file'=>'required|file',
        ]);

        // Wipe the old one
        $medias=$user->medias()->where('tag','avatar')->get() ;
        foreach($medias as $media){
            $media->delete();
        }
        
        $media=$this->laradmin->mediaManager->fromSource($request->file('file') ,'users/avatars',null,'public','public',$user);
        $width=config('laradmin.avatar.width');
        $height=config('laradmin.avatar.height');
        $this->laradmin->mediaManager->constrainSaved($media,$width,$height);

        if(!$media){
            return abort(400,'Image processing issue');
        }
        $user->medias()->save($media,['tag'=>'avatar']);

        // For easy access we put it on the users table
        $user->avatar=$media->url();
        $user->save();

        return 1;
    }

       /**
     * Fetch avatar image in json format
     *
     * @param Request $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function avatarJson(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        $md=null;
        $media=$user->medias()->where('tag','avatar')->first();
        if($media) {  
            $md=[
                'description'=>$media->pivot->description,
                'title'=>$media->pivot->title,
                'url'=>$media->url(false),
                'media_id'=>$media->pivot->media_id,
                'mediable_id'=>$media->pivot->mediable_id,
                'tag'=>$media->pivot->tag,
            ];
        }else{
            //see if there is a social avatar and use it
            if($user->avatar){
                $md=[
                    'description'=>'social avatar',
                    'title'=>'social avatar',
                    'url'=>$user->avatar,
                    'media_id'=>null,
                    'mediable_id'=>$user->id,
                    'tag'=>'avatar',
                ];
            }
        }
        
        return response()->json($md,200,[],JSON_UNESCAPED_SLASHES);
        
    }

     /**
     * Delete current avatar
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function avatarJsonDelete(Request $request)
    {
        
        
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'image'=>'integer',
        ]);
        
        $id=$request->input('image');
        if($id){//id is undefine/null/0 if user is deleting a social avatar
            $media=$user->medias()->where('id',$id)->first()->delete();
        }
            
            

        // Also remove from users table.
        $user->avatar=null;
        $user->save();
        
        return response()->json(1,200,[],JSON_UNESCAPED_SLASHES);

    }
    
}
