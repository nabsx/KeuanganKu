<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTelegramSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chat_id' => ['required', 'string', 'max:50'],
            'bot_token' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'chat_id.required' => 'Chat ID Telegram wajib diisi.',
            'chat_id.max' => 'Chat ID terlalu panjang.',
            'bot_token.max' => 'Bot token terlalu panjang.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
