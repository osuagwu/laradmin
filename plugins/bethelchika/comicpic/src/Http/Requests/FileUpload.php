<?php

namespace BethelChika\Comicpic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUpload extends FormRequest
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
            'file' => 'file|image|mimes:jpg,jpeg,png,bmp,gif,svg',
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
            'file.file' => 'Only files are allowed',
            'file.image'=>'Only extentions jpg, jpeg, png, bmp, gif, or svg are allowed',
            //'file.mimes'=>'Only jpeg, bmp, png, bmp, gif, or svg are allowed',
            
        ];
    }
}
