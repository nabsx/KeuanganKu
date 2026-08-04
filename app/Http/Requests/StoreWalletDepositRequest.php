<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalletDepositRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:1000000000'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ];
    }
}
