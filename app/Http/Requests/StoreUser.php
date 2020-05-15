<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
//            'id' =>  $this->segment(2), //get ID of resource being updated
            'id' =>  auth()->user()->id ?? null, //get ID of resource being updated
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
            'data.type' => ["required", "in:users"],
            'data.attributes' => ["required" ," array"],
            'data.attributes.name' => ['required', 'string', 'max:255'],
            'data.attributes.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->id],
            'data.attributes.password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
