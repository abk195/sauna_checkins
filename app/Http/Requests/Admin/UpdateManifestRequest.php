<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManifestRequest extends FormRequest
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
        $manifest = $this->route('manifest');

        return [
            'manifest_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('manifests', 'manifest_id')->ignore($manifest->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
