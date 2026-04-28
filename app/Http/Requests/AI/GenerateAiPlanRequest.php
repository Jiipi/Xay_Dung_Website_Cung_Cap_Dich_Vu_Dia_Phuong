<?php

namespace App\Http\Requests\AI;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAiPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:20'],
            'budget_amount' => ['nullable', 'integer', 'min:0', 'max:500000000'],
            'budget_label' => ['nullable', 'string', 'max:50'],
            'num_people' => ['required', 'integer', 'min:1', 'max:20'],
            'preferences' => ['nullable', 'array', 'max:10'],
            'preferences.*' => ['string', 'max:50'],
        ];
    }
}
