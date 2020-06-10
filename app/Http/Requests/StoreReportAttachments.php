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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'report_id' => intval($this->get('report_id')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'report_id' => 'required|exists:reports,id',
            'images'    => 'sometimes|array|between:1,10',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:5124',
            'audios'    => 'sometimes|array|between:1,10',
            'audios.*'  => 'mimes:mpga,wav,ogg|max:5124',
        ];
    }
}
