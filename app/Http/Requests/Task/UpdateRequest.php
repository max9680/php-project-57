<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => 'required|unique:tasks,name,' . $this->task->id,
            'description' => '',
            'status_id' => 'required|exists:App\Models\TaskStatus,id',
            'assigned_to_id' => 'nullable|exists:App\Models\User,id',
            'labels' => '',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Это обязательное поле',
            'name.unique' => 'Задача с таким именем уже существует',
            'status_id.required' => 'Это обязательное поле',
        ];
    }
}
