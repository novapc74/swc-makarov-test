<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Выбранный статус недействителен. Допустимые значения: pending, in_progress, completed.',
            'status.required' => 'Статус задачи обязательное поле.',
            'title.required' => 'Заголовок задачи обязателен.',
            'due_date.date_format' => 'Дата должна быть в формате ГГГГ-ММ-ДД.',
            'due_date.required' => 'Дата обязательное поле в формате ГГГГ-ММ-ДД.',
        ];
    }
}
