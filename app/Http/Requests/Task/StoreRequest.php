<?php

namespace App\Http\Requests\Task;

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
            'name' => 'required|string',
            'description' => '',
            'status_id' => 'exists:App\Models\taskStatus,id',
            'created_by_id' => 'exists:App\Models\User,id',
            'assigned_to_id' => 'nullable|exists:App\Models\User,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Это обязательное поле',
        ];
    }
}
