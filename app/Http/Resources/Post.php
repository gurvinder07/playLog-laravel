<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Comment as CommentResource;
use PhpParser\Node\Expr\Array_;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
      {

        $data = collect(CommentResource::collection(collect($this->comments)));


        return [

           'id'=>$this->id,
           'image_id'=>$this->image_id,
           'description'=>$this->description,
           'likes_count'=>$this->likes_count,
            'post_liked'=>$this->postLike->first() !==null? $this->postLike->first()->liked : 0 ,
            'created_at'=> $this->created_at,
            'created_by'=>$this->user['name'],
            'created_by_email'=>$this->user['email'],
            'comments'=> $data->whereNull('parent_id')->take(3)->values(),

            'meta'=>['comment_l1'=>$data->whereNull('parent_id')->count(),
                      'total'=>$data->whereNull('parent_id')->sum('replies_count') + $data->whereNull('parent_id')->count()  ,
                        'has_more_comments'=>  $data->whereNull('parent_id')->count() > 3,
                         'page'=>0,
                           'sub_page'=>0 ]

            ];


    }
}
