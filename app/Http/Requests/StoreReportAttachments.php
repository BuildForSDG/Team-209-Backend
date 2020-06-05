<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportAttachments extends FormRequest
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
            'report_id' => 'required|integer|exists:reports,id',
            'images'    => 'sometimes|array|between:1,10',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:10240',
            'audios'    => 'sometimes|array|between:1,10',
            'audios.*'  => 'mimes:mpga,wav,ogg|max:10240',
        ];
    }
}
