<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'persentase' => ['required', 'array', 'min:1'],
            'persentase.*' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $walletIds = Auth::user()->wallets()->pluck('id')->all();
            $submitted = array_keys($this->input('persentase', []));

            foreach ($submitted as $id) {
                if (! in_array((int) $id, $walletIds, true)) {
                    $validator->errors()->add('persentase', 'Terdapat data wallet yang tidak valid.');
                    break;
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'persentase.required' => 'Persentase wajib diisi.',
            'persentase.*.required' => 'Semua wallet wajib memiliki persentase.',
            'persentase.*.numeric' => 'Persentase harus berupa angka.',
            'persentase.*.min' => 'Persentase tidak boleh kurang dari 0.',
            'persentase.*.max' => 'Persentase tidak boleh lebih dari 100.',
        ];
    }
}
