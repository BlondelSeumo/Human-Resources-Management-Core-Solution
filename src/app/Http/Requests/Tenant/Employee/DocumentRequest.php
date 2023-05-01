<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;

class DocumentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $file = '';
        switch($this->method()) {
            case 'POST':
                $file = 'required|file|mimes:csv,txt,jpg,jpeg,png,pdf,docx,doc,zip|max:5120';
                break;
            case 'PUT':
            case 'PATCH':
                $file = 'nullable|file|mimes:csv,txt,jpg,jpeg,png,pdf,docx,doc,zip|max:5120';
                break;
            default:break;
        }
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'file' => $file
        ];
    }
}