<?php namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PinUpdateRequest extends FormRequest
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
            'old_pin'          => 'required|pin',
            'pin'              => 'required|confirmed|pin',
            'pin_confirmation' => 'required|pin'
        ];
    }
    
    public function messages()
    {
        return [
            'confirmed'    =>  "The pin and confirm pin are not matching",
            'pin'           => "Please enter a valid 4 digit pin."
        ];
    }
}
