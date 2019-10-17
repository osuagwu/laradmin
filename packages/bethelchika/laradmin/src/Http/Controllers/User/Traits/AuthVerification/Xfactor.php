<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification;

use Illuminate\Http\Request;

trait Xfactor
{

    /**
     * Set the extra factor authentication
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function xfactorUpdate(Request $request)
    { 
        $user=$request->user();

        $this->authorize('update', $user);

        $this->validate($request,[
            'xfactor'=>'required|integer|max:50',//The max is just to put an arbitrary limit
        ]);
        
        $user->xfactor=$request->xfactor;
        if($user->save()){
            $msg='Extra factor authentication disabled';
            if($user->xfactor){
                $msg='Extra factor authentication enabled';
            }
            return back()->with('success',$msg); 
        }else{
            return back()->with('danger','Extra factor authentication could not be enabled'); 
        }
        
    }
}
