<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
        $couponId = $this->route('coupon') ? $this->route('coupon')->id : null;
        
        return [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId)
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
            'coupon_data' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'このクーポンコードは既に使用されています。',
            'title.required' => 'クーポンタイトルは必須です。',
            'title.max' => 'クーポンタイトルは255文字以内で入力してください。',
            'description.max' => '説明は1000文字以内で入力してください。',
            'main_image.image' => '画像ファイルを選択してください。',
            'main_image.mimes' => '画像はjpeg、png、jpg、gif形式のみアップロード可能です。',
            'main_image.max' => '画像ファイルは2MB以下にしてください。',
            'expires_at.after' => '有効期限は今日以降の日付を選択してください。',
            'items.array' => 'アイテムは配列形式で選択してください。',
            'items.*.exists' => '選択されたアイテムが存在しません。',
        ];
    }
}
