<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // Post::latest()メソッドでcreated_atの降順
        // ページネーション
        // withメソッドを使用することで、関連するテーブルの情報を取得することが可能
        $posts = Post::with('user')->latest()->paginate(4);
        // dd($posts);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * * @param  \App\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request) {
        $post = new Post($request->all());
        // ログインユーザーのIDを取得
        $post->user_id = $request->user()->id;
        // アップロードする画像の情報を受け取る
        $file = $request->file('image');
        // privateなクラスメソッドを呼び出す場合はself::メソッド名
        $post->image = self::createFileName($file);
        // dd($post);

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $post->save();

            // 画像アップロード(Storage::putFileAs)
            // (保存先のパス, アップロードするファイル, アップロード後のファイル名)
            if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            // backで直前のページ(create.blade.php)にリダイレクトする
            // withInputで入力した値を渡す
            // withErrorsでエラーオブジェクトにエラーメッセージを追加する
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
        ->route('posts.show', $post)->with('notice', '記事を登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $post = Post::find($id);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $post = Post::find($id);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id) {
        $post = Post::find($id);

        // 更新出来なかったら
        // 更新権限をcannotで確認
        if ($request->user()->cannot('update', $post)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分の記事以外は更新できません');
        }
        // 画像がアップロードされているか
        $file = $request->file('image');

        if ($file) {
            // 削除する画像
            // 画像ファイルが指定された時だけ
            $delete_file_path = $post->image_path;
            $post->image = self::createFileName($file);
        }
        $post->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 更新
            $post->save();

            if ($file) {
                // 画像アップロード
                if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }

                // 画像削除
                if (!Storage::delete($delete_file_path)) {
                    //アップロードした画像を削除する
                    Storage::delete($post->image_path);
                    //例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの削除に失敗しました。');
                }
            }

            // トランザクション終了(成功)
            DB::commit();
            // throwの内容をcatchに入る
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            // withInput、テキストボックスの中身
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', '記事を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $post = Post::find($id);

        // トランザクション開始
        DB::beginTransaction();
        try {
            $post->delete();

            // 画像削除
            if (!Storage::delete($post->image_path)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの削除に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.index')
            ->with('notice', '記事を削除しました');
    }

    // (getClientOriginalName)画像ファイル名を取得
    private static function createFileName($file) {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
