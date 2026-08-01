<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:1'],
            'source' => ['required', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'amount.required' => 'Nominal pendapatan wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal minimal Rp1.',
            'source.required' => 'Sumber pendapatan wajib diisi.',
            'source.max' => 'Sumber pendapatan maksimal 150 karakter.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
