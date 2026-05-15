<?php

namespace App\Http\Requests;

use App\Services\LogService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(LogService $service): array
    {
        $selectOptions = $service->getFilterOptions();
        return [
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to' => 'nullable|date|date_format:Y-m-d',
            'os' => [
                'nullable', 'string', Rule::in(data_get($selectOptions, 'os')),
            ],
            'arch' => [
                'nullable', 'string', Rule::in(data_get($selectOptions, 'architectures')),
            ],
            'sort_by' => 'nullable|string|in:log_date,log_count,log_popular_url,log_popular_browser',
            'sort_order' => 'nullable|string|in:asc,desc,ASC,DESC',
        ];
    }
}
