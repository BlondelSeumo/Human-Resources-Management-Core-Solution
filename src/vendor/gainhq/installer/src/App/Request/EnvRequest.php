<?php

namespace Gainhq\Installer\App\Request;

use Gainhq\Installer\App\Helpers\Traits\PasswordMessageHelper;
use Illuminate\Foundation\Http\FormRequest;

class EnvRequest extends FormRequest
{
    use PasswordMessageHelper;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'database_connection' => 'required|in:mysql,pgsql,sqlsrv',
            'database_hostname' => 'required|min:3|regex:/^[^#]+$/U',
            'database_port' => 'required|numeric|min:3|regex:/^[^#]+$/U',
            'database_name' =>  ['required', 'regex:/^\S*$/u', 'regex:/^[^#]+$/U'],
            'database_username' => ['required', 'regex:/^\S*$/u','regex:/^[^#]+$/U'],
            'database_password' => ['nullable', 'regex:/^\S*$/u', 'regex:/^[^#]+$/U'],
        ];
    }
}
