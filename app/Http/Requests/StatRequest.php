<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'car_model_id' => ['required', 'integer', Rule::exists('car_models', 'id')],
            'year_from' => ['required', 'integer', Rule::exists('cars', 'year')],
            'year_to' => ['required', 'integer', Rule::exists('cars', 'year')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'car_model_id.exists' => 'Модель не существует',
            'year_from.exists' => 'Год не существует',
            'year_to.exists' => 'Год не существует',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(['year_from', 'year_to'])) {
                return;
            }

            if ($this->integer('year_from') > $this->integer('year_to')) {
                $validator->errors()->add('year_from', 'Год от должен быть не больше Года до');
            }
        });
    }
}
