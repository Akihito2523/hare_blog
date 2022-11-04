<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

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
        // dd($post);
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
            // DB::rollBack();
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
    public function edit(Post $post, Comment $comment) {
        // dd($comment);

        return view('comments.edit', compact('post', 'comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @param  \App\Models\Post  $post
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Post $post, Comment $comment) {
        // ポリシー
        if ($request->user()->cannot('update', $comment)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分のコメント以外は更新できません');
        }

        $comment->fill($request->all());

        try {
            // 登録
            $comment->save();
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->withInput()->withErrors($th->getMessage());
        }
        // dd($post);

        return redirect()->route('posts.show', $post)
            ->with('notice', 'コメントを更新しました');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post, Comment $comment) {
        // $comment->fill($request->all());
        // トランザクション開始
        // DB::beginTransaction();

        // if ($request->user()->cannot('delete', $comment)) {
        //     return redirect()->route('posts.show', $post)
        //         ->withErrors('自分のコメント以外は削除できません');
        // }
        try {
            $comment->delete();
            // トランザクション終了(成功)
            // DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', 'コメントを削除しました');
    }
}
