<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
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
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>'required|unique:post_translations,name'];
//            $rules +=[$locale.'.body'=>'required'];
        }
        return [

            'name_ar' => 'required|unique:grades,name->ar,'.$this->id,
            'name_en' => 'required|unique:grades,name->en,'.$this->id,
        ];
    }
    public function messages()
    {
        return [
            'name_ar.required' => trans('validation.required'),
            'name_en.required' => trans('validation.required'),
            'name_ar.unique' => trans('validation.unique'),
            'name_en.unique' => trans('validation.unique'),

        ];
    }
}
