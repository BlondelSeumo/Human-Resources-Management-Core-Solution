<?php

namespace Gainhq\Installer\App\Request;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            'full_name' => 'required|min:1',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'regex:/(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*?[+=#?!@$%^&*-])(?!.*\s).*$/U'],
        ];
    }
}
