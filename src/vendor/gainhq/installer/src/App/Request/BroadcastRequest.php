<?php

namespace Gainhq\Installer\App\Request;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules['broadcast_driver'] = 'required';

        if (request()->get('broadcast_driver') === 'pusher') {
            $rules['pusher_app_id'] = 'required';
            $rules['pusher_app_key'] = 'required';
            $rules['pusher_app_secret'] = 'required';
            $rules['pusher_app_cluster'] = 'required';
        }

        return $rules;
    }

}
