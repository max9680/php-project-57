<?php

namespace App\Http\Requests\Label;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:App\Models\Label|string',
            'description' => '',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Метка с таким именем уже существует',
        ];
    }
}
