<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return
        [
            'id'=> $this->id,
            'comment_text'=> $this->comment_text,
            'likes_count'=> $this->likes_count,
            'parent_id'=>$this->parent_id,
            'created_at'=>$this->created_at,
            'replies_count'=>$this->countChildren(),
            'user_name'=>$this->user['name'],
            'email'=>$this->user['email'],
            'children'=>[]

        ];
    }

}
