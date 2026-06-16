<?php
// app/Modules/Chat/Http/Requests/SendMessageRequest.php

namespace App\Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000' ,
            'service_request_id' => 'required|exists:service_requests,id',

            ],
        ];
    }
}
