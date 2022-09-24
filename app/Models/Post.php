<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    use HasFactory;

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\
     */
    public function user() {
        // (belongsTo)1件の記事は1人のユーザーに紐付いている
        return $this->belongsTo(User::class, 'foreign_key', 'other_key');
    }
}
