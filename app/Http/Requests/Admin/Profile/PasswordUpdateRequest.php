<?php namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
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
    
    public function rules()
    {
        return [
            'old_password'          => 'required|min:8',
            'password'              => 'required|confirmed|password',
            'password_confirmation' => 'required|password'

        ];
    }
    
    public function messages()
    {
        return [
            'confirmed'    =>  "The password and confirm password are not matching",
        ];
    }
}
