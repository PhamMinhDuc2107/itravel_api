<?php

namespace App\Http\Requests\Admin\Blog;

use App\Constant\UploadConstant;
use App\Enum\BlogStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($id)
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'image' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,blog_category_id'],
            'author_id' => ['required', 'integer', 'exists:admins,id'],
            'status' => [
                'required',
                'string',
                Rule::enum(BlogStatusEnum::class),
            ],
            'is_featured' => ['nullable', 'integer', 'in:0,1'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'slug.required' => 'Slug is required',
            'slug.unique' => 'Slug already exists',
            'content.required' => 'Content is required',
            'blog_category_id.exists' => 'Blog category does not exist',
            'author_id.required' => 'Author is required',
            'author_id.exists' => 'Author does not exist',
            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status',
            'image.image' => __('validation.custom.avatar.image'),
            'image.mimes' => __('validation.custom.avatar.mimes'),
            'image.max' => __('validation.custom.avatar.max'),
        ];
    }
}

