<?php

namespace Modules\Review\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [            
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
