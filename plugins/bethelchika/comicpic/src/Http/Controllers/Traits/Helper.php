<?php

namespace BethelChika\Comicpic\Http\Controllers\Traits;

use BethelChika\Comicpic\Models\Comicpic;
use BethelChika\Comicpic\Feed\Feed;
use Illuminate\Support\Facades\Log;


trait Helper
{

    /**
     * Deletes single item
     *
     * @param Comicpic $comicpic
     * @param string named route to redirect to
     * @return \Illuminate\Http\Response
     */
    public function destroyer(Comicpic $comicpic,$redirect_to=null)
    {

        $to=$redirect_to;

        if ($comicpic->delete()) {
            if ($comicpic->published_at or $comicpic->published_at) {
                if (Feed::delete($comicpic)) {

                } else {
                    Log::warning('The feed with source_id=' . $comicpic->id . ' and source_type=' . get_class($comicpic) . ' class not deleted when when associated comicpic was deleted. Delete it manually');
                    return $this->to('warning', 'Done with some issues. The associated feed may not have been deleted. Please delete it manually',$to);
                }
            }
            return $this->to('success', 'Done',$to);

        } else {
            return $this->to('danger', 'There was error with the delete action. Please try again.',$to);
        }

    }


    private function to($with,$msg,$to=null){
        if($to){
            return redirect()->route($to)->with($with,$msg);
        }else {
            return back()->with($with,$msg);
        }
    }

    /**
     * Delete multiple rows
     *
     * @param array $comicpic_ids
     * @return \Illuminate\Http\Response
     */
    public function destroyers($comicpic_ids)
    {

        $i = 0;
        $er=0;
        
        foreach ($comicpic_ids as $comicpic_id) {
            $comicpic = Comicpic::find($comicpic_id);

            if(!$comicpic){
                continue;
            }

            if ($comicpic->delete()) {
                $i = $i + 1;
                if ($comicpic->published_at or $comicpic->published_at) {
                    if (Feed::delete($comicpic)) {

                    } else {
                        $er++;
                        Log::warning('The feed with source_id=' . $comicpic->id . ' and source_type=' . get_class($comicpic) . ' class not deleted when when associated comicpic was deleted. Delete it manually');
                        //return back()->with('warning', 'Done with some issues. The associated feed may not have been deleted. Please delete it manually');
                    }
                }
            }

        }
        if($i*$er){
            return back()->with('warning', 'Done with some issues regarding the deletion of associated feeds. See event Log.');
        }elseif($i){
            return back()->with('success', $i.'/'.count($comicpic_ids). ' item(s) sucessfully deleted');
        }
        else {
            return back()->with('info', 'Nothing was deleted');
        }

    }
}