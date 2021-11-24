<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url' => 'required',
            'count_posts' => 'required',
            'access_token' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'url.unique' => 'Группа уже существует',
            'url.required' => 'Группа обязательна',
            'count_posts.required' => 'Количество постов обязательно',
            'access_token.required' => 'Вы не выбрали приложение'
        ];
    }
}
