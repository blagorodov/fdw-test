<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'selected_car_id' => ['required', 'integer'],
            'other_car_id' => ['required', 'integer', 'different:selected_car_id'],
        ];
    }
}
