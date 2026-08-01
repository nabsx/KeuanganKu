<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $wallets = Auth::user()->wallets()->with('allocation')->orderBy('name')->get();

        return view('wallets.index', compact('wallets'));
    }

    public function create(): View
    {
        return view('wallets.create');
    }

    public function store(StoreWalletRequest $request): RedirectResponse
    {
        $wallet = Auth::user()->wallets()->create($request->validated());

        // Buat baris alokasi default 0% supaya langsung muncul di halaman Persentase Wallet.
        $wallet->allocation()->create([
            'user_id' => Auth::id(),
            'percentage' => 0,
        ]);

        return redirect()->route('wallets.index')
            ->with('success', "Wallet \"{$wallet->name}\" berhasil ditambahkan. Jangan lupa atur persentasenya di menu Persentase Wallet agar totalnya 100%.");
    }

    public function show(Wallet $wallet): View
    {
        $this->authorize('view', $wallet);

        $transactions = $wallet->transactions()->paginate(15);

        return view('wallets.show', compact('wallet', 'transactions'));
    }

    public function edit(Wallet $wallet): View
    {
        $this->authorize('update', $wallet);

        return view('wallets.edit', compact('wallet'));
    }

    public function update(UpdateWalletRequest $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('update', $wallet);

        $wallet->update($request->validated());

        return redirect()->route('wallets.index')->with('success', "Wallet \"{$wallet->name}\" berhasil diperbarui.");
    }

    public function destroy(Wallet $wallet): RedirectResponse
    {
        $this->authorize('delete', $wallet);

        $nama = $wallet->name;
        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', "Wallet \"{$nama}\" berhasil dihapus.");
    }
}
