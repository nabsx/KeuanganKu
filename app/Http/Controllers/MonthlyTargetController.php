<?php

namespace App\Http\Controllers;

use App\Models\MonthlyTarget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MonthlyTargetController extends Controller
{
    /**
     * Display form untuk buat/edit target bulanan.
     */
    public function edit(int $month, int $year): View
    {
        $user = Auth::user();

        // Validasi month/year
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2099) {
            abort(422, 'Invalid month or year');
        }

        // Get atau create target
        $target = MonthlyTarget::firstOrNew([
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
        ]);

        $selectedDate = now()
            ->setMonth($month)
            ->setYear($year);

        return view('targets.edit', [
            'target' => $target,
            'month' => $month,
            'year' => $year,
            'selectedDate' => $selectedDate,
        ]);
    }

    /**
     * Store/update target bulanan.
     */
    public function update(int $month, int $year): RedirectResponse
    {
        $user = Auth::user();

        // Validasi
        $validated = request()->validate([
            'target_amount' => 'required|numeric|min:0|max:999999999999',
        ]);

        // Update atau create
        MonthlyTarget::updateOrCreate(
            [
                'user_id' => $user->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'target_amount' => $validated['target_amount'],
            ]
        );

        return redirect()->route('dashboard', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Target tabungan berhasil diupdate');
    }

    /**
     * Delete target.
     */
    public function destroy(int $month, int $year): RedirectResponse
    {
        $user = Auth::user();

        MonthlyTarget::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->delete();

        return redirect()->route('dashboard', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Target tabungan berhasil dihapus');
    }
}
