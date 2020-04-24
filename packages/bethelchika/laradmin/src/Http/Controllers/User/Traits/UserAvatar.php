<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return view('laradmin::user.image.avatar',compact(['pageTitle','user']));
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
        //return $request->all();
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'file'=>'required|file|mimes:jpg,jpeg,png,bmp|max:20000',//TODO: Not tested
            'image_geometry_x'=>'required|numeric',
            'image_geometry_y'=>'required|numeric',
            'image_geometry_width'=>'required|numeric',
            'image_geometry_height'=>'required|numeric',
            'image_geometry_rotate'=>'nullable|numeric',
            'image_geometry_scale_x'=>'required|numeric',
            'image_geometry_scale_y'=>'required|numeric',
        ]);

        // Wipe the old one
        $medias=$user->medias()->where('tag','avatar')->get() ;
        foreach($medias as $media){
            $media->delete();
        }
        
        $media=$this->laradmin->mediaManager->fromSource($request->file('file') ,'users/avatars',null,'public','public',$user);
        
        //Reshape the media how the user wants it.
        $this->laradmin->mediaManager->reshapeImage($media,
                                            intval($request->input('image_geometry_x')),
                                            intval($request->input('image_geometry_y')),
                                            intval($request->input('image_geometry_width')),
                                            intval($request->input('image_geometry_height')),
                                            $request->input('image_geometry_rotate'),
                                            $request->input('image_geometry_scale_x'),
                                            $request->input('image_geometry_scale_y')
                                        );
        
        
        // Now make sure it fits our size spec
        $width=config('laradmin.avatar.width');
        $height=config('laradmin.avatar.height');
        $this->laradmin->mediaManager->constrain($media,$width,$height);

        if(!$media){
            return abort(400,'Image processing issue');
        }
        $user->medias()->save($media,['tag'=>'avatar']);

        // For easy access we put it on the users table
        $user->avatar=$media->url();
        $user->save();

        return response()->json([1],200);
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
                'url'=>$media->url(),
                'media_id'=>$media->pivot->media_id,
                'mediable_id'=>$media->pivot->mediable_id,
                'tag'=>$media->pivot->tag,
            ];
        }else{
            //see if there is a  avatar set on the user table and use it
            if($user->avatar){
                $md=[
                    'description'=>'user avatar',
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
     * Update current avatar
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function avatarJsonUpdate(Request $request)
    {
        //return $request->file('file')[0].'---'.$request->file('file')[1];
        
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'image'=>'integer',
            'image_geometry_x'=>'required|numeric',
            'image_geometry_y'=>'required|numeric',
            'image_geometry_width'=>'required|numeric',
            'image_geometry_height'=>'required|numeric',
            'image_geometry_rotate'=>'nullable|numeric',
            'image_geometry_scale_x'=>'required|numeric',
            'image_geometry_scale_y'=>'required|numeric',
        ]);

        // The the media
        $media=$user->medias()->where('id',$request->input('image'))->first() ;

        $this->laradmin->mediaManager->reshapeImage($media,
                                            intval($request->input('image_geometry_x')),
                                            intval($request->input('image_geometry_y')),
                                            intval($request->input('image_geometry_width')),
                                            intval($request->input('image_geometry_height')),
                                            $request->input('image_geometry_rotate'),
                                            $request->input('image_geometry_scale_x'),
                                            $request->input('image_geometry_scale_y')
                                        );
        
         // Now make sure it fits our size spec
         $width=config('laradmin.avatar.width');
         $height=config('laradmin.avatar.height');
         $this->laradmin->mediaManager->constrain($media,$width,$height);
        

        return response()->json([1],200);
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
        if($id){//id is undefine/null/0 if user is deleting a avatar that is not in the media model
            $media=$user->medias()->where('id',$id)->first()->delete();
        }
            
            

        // Also remove from users table.
        $user->avatar=null;
        $user->save();
        
        return response()->json([1],200);

    }
    
}
