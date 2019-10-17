<?php
namespace BethelChika\Laradmin\Http\Controllers\User\Traits\WP;

use Corcel\Model\Comment;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\WP\Models\LarusPost;
use Illuminate\Support\Carbon;
use BethelChika\Laradmin\WP\Models\Post;

trait PostComment{
/**
     * Creates a comment.
     *
     * @param Request $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function createComment(Request $request){
        $this->validate($request,[
            'post_id'=>'required|integer',
            'parent_id'=>'nullable|integer',
            'comment_content'=>'required|string',
        ]);

        $user=$request->user();

        $post = Post::published()->where('ID', $request->post_id)->first();
        if(!$post){
            abort(404);
        }

        $parent_id=0;
        if($request->parent_id){
            $comment_parent=Comment::find($request->parent_id);
            if(!$comment_parent){
                abort(404);
            }
            $parent_id=$comment_parent->comment_ID;
        }
        
        $comment=new Comment;
        $comment->comment_post_ID=$post->ID;
        $comment->comment_parent=$parent_id;
        $comment->comment_date_gmt=Carbon::now()->toDateTimeString();
        $comment->comment_content= htmlspecialchars($request->comment_content);
        $comment->user_id=$user->id;
        $comment->comment_author_IP=$request->ip();
        $comment->comment_author=$user->id;

        $comment->save();

        return ['data'=>1];
    }

    /**
     * Get comments for Json style
     *
     * @param Request $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function fetchComments(Request $request){
        
        
        //Validate
        $this->validate($request,[
            'post_id'=>'required|integer',
            'parent_id'=>'nullable|integer',
        ]);

        $post_id=intval($request->post_id);
        
        // $parent_id=-1;
        // if($request->has('parent_id')){
        //     $parent_id=$request->parent_id;
        // }
        
        $post = Post::published()->where('ID', $post_id)->first();

        // Check if Larus post can only be read by logged in users
        if($post instanceof LarusPost){ 
            if(!$request->user()){//NOTE that this should not actually happen since the auth middleware will redirect non-authenticated users
                return response()->json('Login to view comments',200,[],JSON_UNESCAPED_SLASHES);
            }
        }
        
        $comments=[];
        $data_out=['currentPageNumber'=>null,
                    'hasMorePages'=>false,
        ];
        
        

        if ($post) {
            $latest_timestamp=null;
            if ($request->has('latest_timestamp')) {
                //Validate by converting to float
                $latest_timestamp=floatval($request->latest_timestamp);
            }
            
            // NOw get the comment
            $comments_q=$post->comments()->latest();
            if (config('laradmin.comment_approve')) {
                //CAUTION:  the comment_approved column is a string
                $comments_q->where('comment_approved', '1');//Fetch only approved comments, but not those in bin and trash.
            }else{
                //CAUTION: the comment_approved column is a string so supply value as strings otherwise strange results.
                $comments_q->whereIn('comment_approved', ['0','1']);// Fetch approved and un approved comments, but not those in bin and trash.
            }

            if($request->has('parent_id')){
                $comments_q->where('comment_parent',$request->parent_id);
            }
            if ($latest_timestamp) { // For getting only comments after the given date
                $comments_q->where('comment_date_gmt', '>', Carbon::createFromTimestamp($latest_timestamp)->toDateTimeString());
            }



            // Paginate
            $limit=5;
            $comments_q=$comments_q->paginate($limit);

            //Fetch the main comment
            $has_items=false;
            foreach ($comments_q as $comment) {//dd($comment);
                $comments[]=$this->exportComment($comment);
                $has_items=true;;
            }

            if($has_items){
                $data_out['currentPageNumber']=$comments_q->currentPage();
                $data_out['hasMorePages']=$comments_q->hasMorePages();
            }


            //Fetch some of the replies
            for($i=0;$i<count($comments);$i++){
                if(rand(0,9)<=2){//So we only get the replies for some of the comments
                    continue;
                }
                $comment_obj=Comment::find($comments[$i]['id']);
                $replies=$comment_obj->replies()->latest();
                
                if (config('laradmin.comment_approve')) {
                    $replies->where('comment_approved', '1');
                }else{
                    //CAUTION: Again the comment_approved column is a string so supply value as strings otherwise strange results.
                    $replies->whereIn('comment_approved', ['0','1']);
                }

                $replies->paginate(round($limit/2));
                $has_items=false;
                foreach($replies as $reply){
                   $comments[$i]['children'][]=$this->exportComment($reply); 
                    $has_items=true;
                }

                $comments[$i]['currentPageNumber']=null;
                $comments[$i]['hasMorePages']=false;
                if($has_items){
                    $comments[$i]['currentPageNumber']=$replies->currentPage();
                    $comments[$i]['hasMorePages']=$replies->hasMorePages();
                }
                
            }

        }
        //dd($comments);
        $data_out['comments']=$comments;
        return response()->json($data_out,200,[],JSON_UNESCAPED_SLASHES);
    }

    /**
     * Convert a comment into a form that we can export
     *
     * @param Comment $comment
     * @return array
     */
    private function exportComment(Comment $comment){
        $commenter=User::find($comment->user_id);
        $commenter_info=['username'=>'Netism','avatar'=>''];
        if ($commenter) {
            $commenter_info=['username'=>$commenter->name,'avatar'=>$commenter->avatar];
        }

        return [
            'id'=>$comment->comment_ID,
            'post_id'=> $comment->comment_post_ID,
            'user'=> $commenter_info,
            'timestamp'=> Carbon::createFromFormat('Y-m-d H:i:s', $comment->comment_date_gmt)->getTimestamp(),
            'date'=> json_encode(Carbon::createFromFormat('Y-m-d H:i:s', $comment->comment_date_gmt)),
            'parent_id'=> $comment->comment_parent,
            'content'=>  htmlspecialchars($comment->comment_content),
            'children'=>[],
            'currentPageNumber'=>0,
            'hasMorePages'=>$comment->hasReplies(),
        ];
    }
}
