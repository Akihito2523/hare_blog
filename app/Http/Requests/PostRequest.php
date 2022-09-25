<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        // storeかupdateかを取得
        $route = $this->route()->getName();

        $rule =  [
            'title' => 'required|string|max:50',
            'body' => 'required|string|max:2000',
        ];

        // 登録時か更新時で且つ画像が指定された時だけ、imageのバリデーションを設定
        if (
            $route === 'posts.store' ||
            ($route === 'posts.update' && $this->file('image'))
        ) {
            // ファイルの拡張子がjpgかpngに該当するか確認
            $rule['image'] = 'required|file|image|mimes:jpg,png';
        }
        return $rule;
    }
}
