<?php

declare(strict_types=1);

namespace Modules\Review\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:Modules\Ecommerce\Models\Order,id'],
            'product_id' => ['required', 'exists:Modules\Ecommerce\Models\Product,id'],
            'variation_option_id' => ['integer', 'exists:Modules\Ecommerce\Models\Variation,id'],
            'comment' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'shop_id' => ['required', 'exists:Modules\Ecommerce\Models\Shop,id'],
            'photos' => ['array'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
