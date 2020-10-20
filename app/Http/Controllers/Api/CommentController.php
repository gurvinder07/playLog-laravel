<?php


namespace App\Http\Controllers\Api;

use App\Http\Resources\Comment as CommentResource;
use App\Http\Resources\SubComment as SubCommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends \Illuminate\Routing\Controller
{

    public function  index($post_id=1 , $page=0)
    {
        $query =  Comment::with('user')
            ->whereNull('parent_id')
            ->where('post_id',$post_id);

        $res = $query->count('*') > ($page + 1)  * 3;

        $data = CommentResource::collection($query
            ->whereNull('parent_id')
            ->skip($page*3)
            ->take(3)
            ->get());

        return $data->additional(['page'=>$page,
                              'has_more_data'=>$res]) ;

        }

       public function  store(Request $request)
       {
            $data = $request->only(['postId','text','parentId']);

           $comment =   Comment::create([
                'comment_text'=>$data['text'],
                'post_id'=>$data['postId'],
                'parent_id'=>$data['parentId'],
                'user_id'=>Auth::id(),
                'likes_count'=>0
            ]);

//            $return_Data = $comment;
            $comment->save();
            return new CommentResource($comment);
       }

       public function  destroy($comment_id)
       {

              $comment = Comment::where('id','=',$comment_id)
                                        ->where('user_id','=',Auth::id());
                    if($comment->first()!=null)
                    {
                        Comment::destroy($comment_id);
                        return \response()->json("",Response::HTTP_NO_CONTENT);
                    }
                    else
                        return \response()->json("Unauthorized",Response::HTTP_UNAUTHORIZED);
       }


       public function loadMoreComments($parent_id)
       {
             $query = Comment::with('children')->where('id','=',$parent_id)->get();
              return  SubCommentResource::collection($query);
       }

}
