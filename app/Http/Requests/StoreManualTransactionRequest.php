<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get wallet dari route parameter
        $wallet = $this->route('wallet');

        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
                function ($attribute, $value, $fail) use ($wallet) {
                    if ($value > $wallet->balance) {
                        $fail('Saldo wallet hanya Rp ' . number_format($wallet->balance, 0, ',', '.') . '. Jumlah pengeluaran tidak boleh melebihi saldo.');
                    }
                },
            ],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah uang keluar wajib diisi.',
            'amount.numeric' => 'Jumlah uang harus berupa angka.',
            'amount.min' => 'Jumlah uang minimal Rp 0,01.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'description.max' => 'Keterangan maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
