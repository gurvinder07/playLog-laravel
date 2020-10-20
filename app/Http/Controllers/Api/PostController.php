<?php


namespace App\Http\Controllers\Api;

use App\Http\Resources\Post as PostResource;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
class PostController extends \Illuminate\Routing\Controller
{


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
            'description' =>'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400) ;
        }
        $uploadFolder = 'posts';
        $image = $request->file('image');
        $image_uploaded_path = $image->store($uploadFolder, 'public');
        $uploadedImageResponse = array(
                "image_name" => basename($image_uploaded_path),
                "image_url" => Storage::disk('public')->url($image_uploaded_path),
                "mime" => $image->getClientMimeType()
 );

     $post =  Post::create([
            "image_id"=>basename($image_uploaded_path),
            "description"=>$request->description,
            "likes_count"=>0,
            "user_id"=>Auth::id()
        ]);
     $post->save();
     $returnResponse = Post::with('user','comments')->with(array('postLike' =>
         function ($query) {
             $query->select('post_id', 'user_id', 'liked')
                 ->where('user_id', Auth::id());
         }))->where('id',$post->id)->get()->first();

     return new PostResource($returnResponse);
    }

    public function index($page_id = 0)

    {
        $posts = Post::with('user', 'comments')
            ->with(array('postLike' =>
                function ($query) {
                    $query->select('post_id', 'user_id', 'liked')
                        ->where('user_id', Auth::id());
                }));

        $total_count = $posts->count('*');
        $data = PostResource::collection($posts->skip($page_id * 10)
            ->take(10)
            ->get());
        return $data->additional(["total_posts" => $total_count]);

    }

        public function storePostLikeInfo($post_id)
        {


            $like_data = PostLike::where('post_id', '=', $post_id)
                ->where('user_id', '=', Auth::id())
                ->get()->first();


            $post = Post::find($post_id);
            if ($like_data != null) {

                if ($like_data->liked == 0)
                    $post->likes_count = $post->likes_count + 1;
                else
                    $post->likes_count = $post->likes_count - 1;

                $like_data->liked = !$like_data->liked;
                $like_data->save();


            } else {

                PostLike::create([
                    'user_id' => Auth::id(),
                    'post_id' => $post_id,
                    'liked' => true
                ])->save();
                $post->likes_count = $post->likes_count + 1;
            }
            $post->save();
            return response('', Response::HTTP_NO_CONTENT);

    }

    public function destroy($id)

    {
        if (Post::find($id)->user_id == Auth::id()) {

            $res = \App\Models\Post::destroy($id);
            if ($res == 1)
                return response([], 204);
            return response([], 404);
        } else {
                return  \response("Unauthorized",Response::HTTP_UNAUTHORIZED);
        }
    }
}


