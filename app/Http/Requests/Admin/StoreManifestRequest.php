<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreManifestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'manifest_id' => ['required', 'string', 'max:255', 'unique:manifests,manifest_id'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
