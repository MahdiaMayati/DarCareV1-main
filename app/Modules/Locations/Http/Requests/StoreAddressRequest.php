<?php
// app/Modules/Locations/Http/Requests/StoreAddressRequest.php

namespace App\Modules\Locations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'label'     => ['nullable', 'string', 'in:home,work,other'],
        ];
    }
}
