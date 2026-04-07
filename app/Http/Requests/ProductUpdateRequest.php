<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:products,name,' . $this->product->id,
            'detail' => 'required|string',
            // Existing variant updates
            'existing_image_data'           => 'nullable|array',
            'existing_image_data.*.color_id' => 'nullable|exists:colors,id',
            'existing_image_data.*.size_id'  => 'nullable|exists:sizes,id',
            'existing_image_data.*.price'    => 'nullable|numeric|min:0',
            'existing_image_data.*.stock'    => 'nullable|integer|min:0',
            'existing_variant_files'         => 'nullable|array',
            'existing_variant_files.*'       => 'nullable|array',
            'existing_variant_files.*.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',

            // New variant images
            'image_data'               => 'nullable|array',
            'image_data.*.files'       => 'nullable|array',
            'image_data.*.files.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_data.*.color_id'    => 'nullable|exists:colors,id',
            'image_data.*.size_id'     => 'nullable|exists:sizes,id',
            'image_data.*.price'       => 'nullable|numeric|min:0',
            'image_data.*.stock'       => 'nullable|integer|min:0',
            'status'                => 'required|in:active,inactive,deleted',
            'category_id'           => 'required|exists:categories,id',
            'size_id'               => 'nullable|array',
            'size_id.*'             => 'exists:sizes,id',
            'color_id'              => 'nullable|array',
            'color_id.*'            => 'exists:colors,id',
            'price' => 'required|numeric|min:0',
            'stock'                => 'nullable|integer|min:0',
            'seo_meta_title'       => 'nullable|string|max:255',
            'seo_meta_description' => 'nullable|string',
            'seo_meta_key'         => 'nullable|string|max:255',
            'seo_meta_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_canonical'        => 'nullable|string|max:255',
            'og_meta_title'        => 'nullable|string|max:255',
            'og_meta_description'  => 'nullable|string',
            'og_meta_key'          => 'nullable|string|max:255',
            'og_meta_image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists. Please choose a different name.',
        ];
    }
}
