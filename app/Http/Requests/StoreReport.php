<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReport extends FormRequest
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
            'data.type'       => ["required", "in:reports"],
            'data.attributes' => ["required" ," array"],
            'data.attributes.latitude'  => ['required', 'string'],
            'data.attributes.longitude'  => ['required', 'string'],
            'data.attributes.description' => ['required', 'string'],
        ];
    }
}
