<?php

namespace BethelChika\Comicpic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileDetails extends FormRequest
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
            'title' => 'required|max:255',
            'description' => 'required|max:2000',
            'tags'=>'max:100',
            'hashtags'=>'max:100',
            'twitter_via'=>'max:100',
            'twitter_screen_names'=>'max:100'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'A title is required',
            'description.required'  => 'A message is required',
            'description.max'=>'Description should not be more than 2000 characters',
            'index_tags.max'=>'Tags must not be more than a total of 100 characters',
            'hashtags.max'=>'Hashtags must not be more than a total of 100 characters',
            'twitter_via'=>'Twitter via must not be more than a total of 100 characters',
            'twitter_screen_names'=>'Twitter screen names must not be more than a total of 100 characters'
        ];
    }
}
