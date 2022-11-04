<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-8 py-4 bg-white shadow-md">

        <x-flash-message :message="session('notice')" />
        {{-- エラーメッセージを取得 --}}
        <x-validation-errors :errors="$errors" />

        <article class="mb-2">
            <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">
                {{ $post->title }}</h2>
            {{-- Postモデルにpublic function user()を定義しているため --}}
            <h3>{{ $post->user->name }}</h3>
            <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                {{-- 三項演算子 --}}
                <span
                    class="text-red-400 font-bold">{{ date('Y-m-d H:i:s', strtotime('-1 day')) < $post->created_at ? 'NEW' : '' }}</span>
                {{ $post->created_at }}
            </p>
            <img src="{{ $post->image_url }}" alt="" class="mb-4">
            <p class="text-gray-700 text-base">{!! nl2br(e($post->body)) !!}</p>
        </article>
        <div class="flex flex-row text-center my-4">

            {{-- 自分が投稿した記事の場合のみ、編集ボタンと削除ボタンを表示 --}}
            {{-- 自分が投稿した記事の場合 --}}
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
            @endcan
            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                </form>
            @endcan
        </div>

        {{-- ログイン状態の時のみ、照会画面にコメント登録のボタンを表示 --}}
        @auth
            <hr class="my-4">

            <div class="flex justify-end">
                <a href="{{ route('posts.comments.create', $post) }}"
                    class="bg-indigo-400 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline block">コメント登録</a>
            </div>
        @endauth

        {{-- コメント投稿者の氏名,投稿日時,コメントの本文 --}}
        <section class="font-sans break-normal text-gray-900 ">
            @foreach ($comments as $comment)
                <div class="my-2">
                    <span class="font-bold mr-3">{{ $comment->user->name }}</span>
                    <span class="text-sm">{{ $comment->created_at }}</span>
                    <p>{!! nl2br(e($comment->body)) !!}</p>

                    {{-- ポリシー自分が投稿したコメントだけ編集と削除のリンクが表示 --}}
                    <div class="flex justify-end text-center">
                        {{-- @can('update', $comment) --}}
                            <a href="{{ route('posts.comments.edit', [$post, $comment]) }}"
                                class="text-sm bg-green-400 hover:bg-green-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
                        {{-- @endcan --}}

                        @can('delete', $comment)
                            <form action="{{ route('posts.comments.destroy', [$post, $comment]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};"
                                    class="text-sm bg-red-400 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline w-20">
                            </form>
                        @endcan
                    </div>

                </div>
                <hr>
            @endforeach
        </section>

    </div>
</x-app-layout>
