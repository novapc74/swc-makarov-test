<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    private array $statuses = ['pending', 'in_progress', 'completed'];

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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:' . implode(',', $this->statuses),
            'due_date' => 'nullable|date_format:Y-m-d|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Выбранный статус недействителен. Допустимые значения: ' . implode(', ', $this->statuses),
            'title.string' => 'Формат заголовка - строка.',
            'description.string' => 'Формат описания задачи - строка.',
            'due_date.date_format' => 'Дата должна быть в формате ГГГГ-ММ-ДД.',
            'due_date.after_or_equal' => 'Срок выполнения не может быть в прошлом.',
        ];
    }
}
