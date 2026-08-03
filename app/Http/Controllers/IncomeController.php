<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Jobs\SendTelegramNotification;
use App\Models\WalletAllocation;
use App\Services\IncomeAllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function __construct(
        private IncomeAllocationService $allocationService,
    ) {
        // TelegramService tidak lagi di-inject langsung di sini,
        // karena pengiriman notifikasi sekarang lewat Job (queue).
    }

    public function index(): View
    {
        $incomes = Auth::user()->incomes()->latest('date')->latest('id')->paginate(15);

        return view('incomes.index', compact('incomes'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $totalPersentase = round((float) WalletAllocation::where('user_id', $user->id)->sum('percentage'), 2);
        $walletCount = $user->wallets()->count();

        return view('incomes.create', compact('totalPersentase', 'walletCount'));
    }

    public function store(StoreIncomeRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->wallets()->count() === 0) {
            return back()->withInput()->with('error', 'Anda belum memiliki wallet. Silakan tambah wallet terlebih dahulu sebelum mencatat pendapatan.');
        }

        $totalPersentase = round((float) WalletAllocation::where('user_id', $user->id)->sum('percentage'), 2);

        if ($totalPersentase !== 100.00) {
            SendTelegramNotification::dispatch(
                $user,
                "⚠️ Peringatan: Total persentase wallet Anda saat ini {$totalPersentase}%, belum tepat 100%. Percobaan mencatat pendapatan ditolak."
            );

            return back()->withInput()->with('error', "Total persentase wallet Anda saat ini {$totalPersentase}%. Total harus tepat 100% sebelum pendapatan bisa dicatat. Silakan atur di menu Persentase Wallet.");
        }

        $income = DB::transaction(function () use ($request, $user) {
            $income = $user->incomes()->create($request->validated());
            $this->allocationService->allocate($income);

            return $income;
        });

        SendTelegramNotification::dispatch(
            $user,
            "✅ Pendapatan baru berhasil dicatat!\nSumber: {$income->source}\nNominal: Rp".number_format((float) $income->amount, 0, ',', '.')
                ."\nTanggal: {$income->date->format('d-m-Y')}"
        );

        return redirect()->route('incomes.index')->with('success', 'Pendapatan berhasil dicatat dan otomatis dibagikan ke semua wallet sesuai persentase.');
    }
}