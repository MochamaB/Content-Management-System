<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'phonenumber' => 'required|unique:users,phonenumber',
            'idnumber' => 'numeric',
          //  'password' => 'required|min:6', // Minimum length of 6 characters
            'status' => 'required',
            'profilepicture' => 'required',
            

        ];
    }
}
