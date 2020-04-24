<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits;
use \Illuminate\Http\Request;

/**
 * Adds capability to handle cover photo
 */
trait UserCoverPhoto
{
    /**
     * The main entrance to editing cover photo
     * 
    * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function coverPhoto(Request $request)
    {
        $user=$request->user();
        $this->authorize('update', $user);

        $pageTitle='Cover photo';
        return view('laradmin::user.image.cover_photo',compact(['pageTitle','user']));
    }
    
    /**
     * Saves new or replaces current cover photo
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function coverPhotoJsonStore(Request $request)
    {

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
        $medias=$user->medias()->where('tag','cover-photo')->get() ;
        foreach($medias as $media){
            $media->delete();
        }
        
        $media=$this->laradmin->mediaManager->fromSource($request->file('file') ,'users/cover-photos',null,'public','public',$user);
        

        if(!$media){
            return abort(400,'Image processing issue');
        }

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
        $width=config('laradmin.cover_photo.width');
        $height=config('laradmin.cover_photo.height');
        $this->laradmin->mediaManager->constrain($media,$width,$height);

        // Create a version for mobile
        $media->makeImageSizes('_cover_photo_sm_','aspect');


        
        $user->medias()->save($media,['tag'=>'cover-photo']);

        // For easy access we put it on the users table
        // $user->cover_photo=$media->url();
        // $user->save();

        return response()->json([1],200);
    }

       /**
     * Fetch cover photo image in json format
     *
     * @param Request $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function coverPhotoJson(Request $request){
        $user=$request->user();
        $this->authorize('update', $user);

        $md=null;
        $media=$user->medias()->where('tag','cover-photo')->first();
        if($media) {  
            $md=[
                'description'=>$media->pivot->description,
                'title'=>$media->pivot->title,
                'url'=>$media->url(),
                'media_id'=>$media->pivot->media_id,
                'mediable_id'=>$media->pivot->mediable_id,
                'tag'=>$media->pivot->tag,
            ];
        // }else{
        //     //see if there is a cover photo set on the user table and use it
        //     if($user->cover_photo){
        //         $md=[
        //             'description'=>'user cover photo',
        //             'title'=>'cover photo',
        //             'url'=>$user->cover_photo,
        //             'media_id'=>null,
        //             'mediable_id'=>$user->id,
        //             'tag'=>'cover_photo',
        //         ];
        //     }
         }
        
        return response()->json($md,200,[],JSON_UNESCAPED_SLASHES);
        
    }

    /**
     * Update current cover photo
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function coverPhotoJsonUpdate(Request $request)
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

        // The media
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
         $width=config('laradmin.cover_photo.width');
         $height=config('laradmin.cover_photo.height');
         $this->laradmin->mediaManager->constrain($media,$width,$height);
        
         // Create a version for mobile
         $media->makeImageSizes('_cover_photo_sm_','aspect');
        

        return response()->json([1],200);
    }

     /**
     * Delete current cover photo
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function coverPhotoJsonDelete(Request $request)
    {
        
        
        $user=$request->user();
        $this->authorize('update', $user);

        $this->validate($request,[
            'image'=>'integer',
        ]);
        
        $id=$request->input('image');
        if($id){//id is undefine/null/0 if user is deleting a cover photo that has not media model
            $media=$user->medias()->where('id',$id)->first()->delete();
        }
            
            

        // Also remove from users table.
        $user->cover_photo=null;
        $user->save();
        
        return response()->json([1],200);

    }
    
}
