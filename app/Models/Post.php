<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model {
    use HasFactory;

    // (fillable)代入を許可するカラムを指定する
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\
     */
    public function user() {
        // (belongsTo)1件の記事は1人のユーザーに紐付いている
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    // 画像のパスは、Postモデルのインスタンス
    public function getImageUrlAttribute() {
        return Storage::url($this->image_path);
    }
// アクセサの追加
    public function getImagePathAttribute() {
        return 'images/posts/' . $this->image;
    }
}
