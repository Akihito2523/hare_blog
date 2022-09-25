<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Post $post) {
        return view('comments.create', compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, Post $post) {
        $comment = new Comment($request->all());
        $comment->user_id = $request->user()->id;

        // $comment->post_id = $post->id;

        // トランザクション開始
        // DB::beginTransaction();
        try {
            // 登録
            // bodyとidがcommentに入る
            // $comment->save();
            $post->comments()->save($comment);
            // DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->getMessage());
        }
        // $comment->save();
        return redirect()
            ->route('posts.show', $post)
            ->with('notice', 'コメントを登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment) {
        //
    }
}
