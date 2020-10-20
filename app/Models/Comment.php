<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
     public $fillable=[

                 'comment_text',
                 'post_id',
                 'user_id',
                 'likes_count',
                 'parent_id',
                 'created_at',
                 'updated_at'
     ];

    public function getDateFormat()
    {
        return 'U';
    }


public function post()
{
    return $this->belongsTo(Post::class);

}


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return   $this->hasMany( Comment::class, 'parent_id', 'id' )->with('children');
    }


    public function countChildren($node = null)
    {
        $query = $this->children();
        if (!empty($node))
        {
            $query = $query->where('node', $node);
        }

        $count = 0;
        foreach ($query->get() as $child)
        {
            $count += $child->countChildren() + 1; // Plus 1 to count the direct child
        }
        return $count;
    }
}
