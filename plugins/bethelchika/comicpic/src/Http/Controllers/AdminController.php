<?php

namespace BethelChika\Comicpic\Http\Controllers;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Cache;
use BethelChika\Comicpic\Models\Comicpic;
use BethelChika\Comicpic\Feed\Feed;
use Illuminate\Support\Facades\Auth;
use BethelChika\Comicpic\Http\Controllers\Traits\Helper;


class AdminController extends Controller
{
    use Helper;
    
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        $this->middleware(['auth','pre-authorise']);
        
        $this->laradmin = $laradmin;
        $this->appName = Cache::get('comicpic.appname', 'Comicpic');
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle = 'Data view';
        $appname = $this->appName;




        $request = Request();

        $order_by = $request->get('order_by', 'id');
        $order_by_dir = $request->get('order_by_dir', 'asc');
        $currentOrder = $order_by . ':' . $order_by_dir;

        if ($request->search) {
            $search_str = '%' . $request->get('comicpics_search') . '%';
            $comicpics = Comicpic::where('title', 'like', $search_str)->orderBy($order_by, $order_by_dir)->paginate(10);
            $request->flash('comicpics_search');
        } else {

            $comicpics = Comicpic::orderBy($order_by, $order_by_dir)->paginate(10);
        }
        //return view('laradmin::cp.users',['users'=>$users,'currentOrder'=>$currentOrder]);


        return $this->laradmin->pluginManager->adminView('comicpic::admin.index', compact('pageTitle', 'appname', 'comicpics', 'currentOrder'));
    }
    /**
     * Show
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Comicpic $comicpic)
    {
        $pageTitle = $comicpic->title;
        return $this->laradmin->pluginManager->adminView('comicpic::admin.show', compact('comicpic', 'pageTitle'));
    }
    /**
     * Deletes a record specified
     *
     * @param Comicpic $comicpic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comicpic $comicpic)
    {
        $user = Auth::user();
        $this->authorize('delete', $user);//Basically , if the can delete user, then can delete comicpic

        return $this->destroyer($comicpic,'comicpic.admin');
        // if($comicpic->delete()){
        //     Feed::delete($comicpic);
        //     return redirect()->route('comicpic.admin')->with('success','Done');
        // }else{
        //     return redirect()->route('comicpic.admin')->with('danger','There was error with the delete action. Please try again.');
        // }

    }
    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroys(Request $request)
    {
        $user = Auth::user();
        $this->authorize('delete', $user);//Basically , if the can delete user, then can delete comicpic


        
        $comicpic_ids=explode(',', $request->comicpics_ids) ;
        return $this->destroyers($comicpic_ids);

    }

    /**
     * Edit settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function editSettings(Laradmin $laradmin)
    {
        $pageTitle = 'Comicpic settings';
        $appname = $this->appName;
        return $this->laradmin->pluginManager->adminView('comicpic::admin.edit_settings', compact('pageTitle', 'appname'));
    }

    /**
     * Edit application
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request, Laradmin $laradmin)
    {
        //FIXME:: Consider adding authorization here.

        $pageTitle = 'Comic Pic settings';
        if ($request->has('appname')) {
            $appname = Cache::forever('comicpic.appname', $request->appname);
            return back()->with('success', 'Updated');
        }

        return back();

    }

    /**
     * Show the application settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        
        //$pageTitle='Settings';
        //return view('comicpic::setting',compact('pageTitle'));
    }

    /**
     * About page
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        // $pageTitle='About us';
        // $bodyClasses=config('laradmin.css_classes')['body_hero'];
        // return view('about',compact('pageTitle','bodyClasses'));
    }


}
