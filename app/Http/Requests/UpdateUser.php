<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
            'data.type' => ["required", "in:users"],
            'data.id' => ["required"],
            'data.attributes' => ["required" ,"array"],
            'data.attributes.name' => ['sometimes', 'string', 'max:255'],
            'data.attributes.email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email'],
        ];
    }
}
