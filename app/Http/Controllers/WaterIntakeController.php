<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterIntake;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WaterIntakeController extends Controller
{
    /**
     * 🔹 Simpan konsumsi air (default 250ml per klik, max 2000ml per hari per user)
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $todayDate = Carbon::today('Asia/Jakarta')->toDateString();
            $jumlahBaru = $request->input('jumlah_ml', 250);

            // Validasi batas konsumsi harian
            $waterIntake = WaterIntake::where('user_id', $user->id)
                ->where('tanggal', $todayDate)
                ->first();

            if ($waterIntake) {
                if (($waterIntake->jumlah_ml + $jumlahBaru) > 2000) {
                    return response()->json([
                        'message' => 'Batas konsumsi air hari ini telah tercapai',
                        'total_ml_today' => $waterIntake->jumlah_ml,
                        'max_reached' => true
                    ], 400);
                }

                // Update jumlah_ml
                $waterIntake->jumlah_ml += $jumlahBaru;
                $waterIntake->save();
                $totalToday = $waterIntake->jumlah_ml;
                $entry = $waterIntake;
            } else {
                // Insert baru
                $entry = WaterIntake::create([
                    'user_id' => $user->id,
                    'jumlah_ml' => $jumlahBaru,
                    'tanggal' => $todayDate,
                ]);
                $totalToday = $jumlahBaru;
            }

            return response()->json([
                'message' => 'Konsumsi air berhasil disimpan',
                'data' => $entry,
                'total_ml_today' => $totalToday,
                'max_reached' => false
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan data air', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Tampilkan riwayat konsumsi air 7 hari terakhir (termasuk hari ini)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $today = Carbon::today('Asia/Jakarta');
            $history = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i)->toDateString();
                $jumlah = WaterIntake::where('user_id', $user->id)
                    ->where('tanggal', $date)
                    ->value('jumlah_ml') ?? 0;

                $history[] = [
                    'tanggal' => $date,
                    'jumlah_ml' => $jumlah,
                ];
            }

            $totalKonsumsiHariIni = WaterIntake::where('user_id', $user->id)
                ->where('tanggal', $today->toDateString())
                ->value('jumlah_ml') ?? 0;

            return response()->json([
                'message' => 'Data riwayat berhasil diambil',
                'total_konsumsi' => $totalKonsumsiHariIni,
                'history' => $history,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Gagal mengambil riwayat air', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
