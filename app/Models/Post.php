<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
 public $fillable=[
           'image_id',
           'description',
           'user_id',
           'likes_count'
     ];


    public function getDateFormat()
    {
        return 'U';
    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function postLike()
    {
        return $this->hasMany(PostLike::class);
    }
}
