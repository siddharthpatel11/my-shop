<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'detail' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,deleted',
            'category_id' => 'required|exists:categories,id',
            // 'size_id' => 'nullable|exists:sizes,id',
            // 'color_id' => 'nullable|exists:colors,id',
            'size_id'     => 'nullable|array',
            'size_id.*'   => 'exists:sizes,id',
            'color_id'    => 'nullable|array',
            'color_id.*'  => 'exists:colors,id',
            'price' => 'required|numeric|min:0',
        ];
    }
}
