<?php

namespace BethelChika\Laradmin\Permission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\User;

class PreAuthorise
{
    /* This class applies the Url authorisation to all rrquests


     */

     /**
      * Laradmin
      *
      * @var \BethelChika\Laradmin\Laradmin
      */
      private $laradmin;

    /**
     * Permission instance
     *
     * @var \BethelChika\Laradmin\Permission\Permission
     */
    private $perm;

    /**
     * User instance
     *
     * @var \BethelChika\Laradmin\User
     */
    private $user;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // First is this a WP page and if so are we allowed to authorise it?
        $prefix=app('router')->current()->getPrefix();
        $page_prefix='/'.trim(config('laradmin.page_url_prefix'),'/') ;
        if( !config('laradmin.wp_page_auth') and !strcmp($prefix,$page_prefix)){
             return $next($request);
        }
        

        
        $this->laradmin=app('laradmin');

       $this->perm=$this->laradmin->permission;
        $this->user=$request->user();

        // Begin Authorisation 

       //return $this->perm->can($user,'table:users','read',$userToView);
       //$perm->can();

       if($this->authoriseUrl($request) and $this->authoriseRoute()){
            return $next($request);
       }else{
           //return redirect()->route('user-profile')->with('warning','Access denied');
           abort(403);
       }
        
       
        
    }

    /**
     * AUthorise URL
     *
     * @param Request $request
     * @return boolean
     */
    private function authoriseUrl(Request $request){
        
        // first split the url into parts
        $urls_=explode('/',$request->url());
        $urls=[];
        for($i=0;$i<count($urls_);$i++){
            $urls[]=trim(implode('/',array_slice($urls_,0,$i+1)),'/:');//The trim  actually only helps to cleanly extract the protocol part of the url without forward slash and colon.
        }
        $urls=array_unique($urls);//Removes duplicate of the protocol part of the url
        
        // find each part of the url
        $source_items=Source::where('type','url')
        ->where(function($query)use ($urls){
            foreach($urls as $url){
                $query->orwhere('name',$url);
            }
        });
        //\Illuminate\Support\Facades\DB::enableQueryLog();
        $source_items=$source_items->get();
        //dd(\Illuminate\Support\Facades\DB::getQueryLog());

        // Return true if nothing is found
        if(!count($source_items)){
            return true;
        }

        //Now perform test
        foreach($source_items as $source_item){
            //$source_type=Source::getTypeKey($source_item);
            //$source=$source_type.':'.$source_item->id;
             // Check if a user must login first
            $user=$this->user;
            if(!$user){
                if($this->perm->hasDenyEntry(Source::class,$source_item->id,'read')){
                    abort(403,'You must login to access the request.');
                }
                $user=User::getGuestUser();// TODO: if there is no entry should we actually border trying to authorise at all against the guest user?
            }

            if($this->perm->isDisallowed($user,Source::class,$source_item->id,'read')){
                return false;
            }
            
        }
        return true;
    }

    /**
     * Authorise Route
     *
     * @return boolean
     */
    private function authoriseRoute(){
        $route=app('router')->current();
       
        //Check route
        $source_id=Source::getRouteSourceId($route);
        $source_type='route';

         // Check if a user must login first
         $user=$this->user;
         if(!$user){
             if($this->perm->hasDenyEntry($source_type,$source_id,'read')){
                 abort(403,'You must login to access the request.');
             }
             $user=User::getGuestUser();// TODO: if there is no entry should we actually border trying to authorise at all against the guest user?
         } 

         // Now do permission
        if($this->perm->isDisallowed($user,$source_type,$source_id,'read')){
            return false;
        }




        //
        //******************************************************************
        //CHeck prefix****************************************************
        $prefix=$route->getPrefix();
        
        // If is possible for a developer to not properly put a cp route within the cp prefix. Lets
        // help such developer here and check if the route should be cp. So if the resource path
        // begins with /cp we will assume it is cp. OPEN THE CODE BELOW to HELP THE DEVELOPER.
         if(!$prefix and $this->laradmin->isCp()){ // Note that it is not documented that this help will be provided. We can document it after we find a way to not hardcode the \cp route prefix.
             $prefix='/cp';//HARDCODING: route prefix
         }
        



        if(!$prefix){
            return true;
        }
        $source_type='route_prefix';//Source::getRoutePrefixTypeKey();
        $source_id=$prefix;

        // Check if a user must login first
        $user=$this->user;
        if(!$user){
            if($this->perm->hasDenyEntry($source_type,$source_id,'read')){
                abort(403,'You must login to make this request.');
            }
             
            $user=User::getGuestUser();// TODO: if there is no entry should we actually border trying to authorise at all against the guest user?
        }

        //Now check permission
        if($this->laradmin->isCp()){ //We check \cp prefix specially.
            if(!$this->perm->can($user,$source_type,$source_id,'read')){
                return false;
            }
        }else{
            if($this->perm->isDisallowed($user,$source_type,$source_id,'read')){
                return false;
            }
        }
        

        return true;

    }

  
}
