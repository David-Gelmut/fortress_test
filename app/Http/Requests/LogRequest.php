<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
    public function rules(): array
    {
        return [
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to' => 'nullable|date|date_format:Y-m-d',
            'os' => 'nullable|string|in:Windows,Linux,macOS,Android,iOS',
            'arch' => 'nullable|string|in:x86_64,arm64,x86',
            'sort_by' => 'nullable|string|in:log_date,log_count,log_popular_url,log_popular_browser',
            'sort_order' => 'nullable|string|in:asc,desc,ASC,DESC',
        ];
    }
}
