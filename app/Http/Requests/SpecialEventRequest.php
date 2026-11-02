<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * チェックボックスは未チェック時にリクエストに含まれないため、デフォルト0をセット
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'has_timer' => $this->boolean('has_timer'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'event_date' => array_filter(['required', 'date', $this->isMethod('POST') ? 'after_or_equal:today' : null]),
            'start_hour' => 'required|integer|min:0|max:23',
            'start_minute' => 'required|integer|min:0|max:59',
            'end_hour' => 'required|integer|min:0|max:23',
            'end_minute' => 'required|integer|min:0|max:59',
            'description' => 'required|string',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fee' => 'nullable|integer|min:0',
            'fee_note' => 'nullable|string|max:500',
            'venue' => 'nullable|string|max:255',
            'access_info' => 'nullable|string|max:1000',
            'map_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'external_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'has_timer' => 'boolean',
            'timer_start' => 'nullable|date|required_if:has_timer,true',
            'timer_end' => 'nullable|date|after:timer_start|required_if:has_timer,true',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'event_date.required' => '開催日は必須です。',
            'event_date.date' => '開催日は有効な日付を入力してください。',
            'event_date.after_or_equal' => '開催日は今日以降の日付を選択してください。',
            'start_hour.required' => '開始時間（時）は必須です。',
            'start_hour.integer' => '開始時間（時）は数値で入力してください。',
            'start_hour.min' => '開始時間（時）は0以上で入力してください。',
            'start_hour.max' => '開始時間（時）は23以下で入力してください。',
            'start_minute.required' => '開始時間（分）は必須です。',
            'start_minute.integer' => '開始時間（分）は数値で入力してください。',
            'start_minute.min' => '開始時間（分）は0以上で入力してください。',
            'start_minute.max' => '開始時間（分）は59以下で入力してください。',
            'end_hour.required' => '終了時間（時）は必須です。',
            'end_hour.integer' => '終了時間（時）は数値で入力してください。',
            'end_hour.min' => '終了時間（時）は0以上で入力してください。',
            'end_hour.max' => '終了時間（時）は23以下で入力してください。',
            'end_minute.required' => '終了時間（分）は必須です。',
            'end_minute.integer' => '終了時間（分）は数値で入力してください。',
            'end_minute.min' => '終了時間（分）は0以上で入力してください。',
            'end_minute.max' => '終了時間（分）は59以下で入力してください。',
            'description.required' => '内容は必須です。',
            'item_ids.array' => '参加キャストは配列形式で選択してください。',
            'item_ids.*.exists' => '選択されたキャストが存在しません。',
            'main_image.image' => '画像ファイルを選択してください。',
            'main_image.mimes' => '画像はjpeg、png、jpg、gif形式のみアップロード可能です。',
            'main_image.max' => '画像ファイルは2MB以下にしてください。',
            'fee.integer' => '料金は数値で入力してください。',
            'fee.min' => '料金は0以上で入力してください。',
            'fee_note.max' => '料金注意事項は500文字以内で入力してください。',
            'venue.max' => '場所は255文字以内で入力してください。',
            'access_info.max' => 'アクセス案内は1000文字以内で入力してください。',
            'map_url.url' => 'Google Map URLは有効なURLを入力してください。',
            'map_url.max' => 'Google Map URLは500文字以内で入力してください。',
            'external_link.url' => '外部リンクは有効なURLを入力してください。',
            'external_link.max' => '外部リンクは500文字以内で入力してください。',
            'notes.max' => '備考は1000文字以内で入力してください。',
            'timer_start.required_if' => 'タイマー表示を有効にする場合は表示開始日を入力してください。',
            'timer_end.required_if' => 'タイマー表示を有効にする場合は表示終了日を入力してください。',
            'timer_end.after' => '表示終了日は表示開始日より後の日付を入力してください。',
            'sort_order.integer' => 'ソート順は数値で入力してください。',
            'sort_order.min' => 'ソート順は0以上で入力してください。',
        ];
    }
}
