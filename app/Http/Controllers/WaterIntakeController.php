<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterIntake;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Support\AuthToken;

class WaterIntakeController extends Controller
{
    // Konstanta target hidrasi harian
    const TARGET_ML_PER_DAY = 2000; // 8 gelas × 250ml
    const ML_PER_GLASS = 250;
    const TARGET_GLASSES = 8;

    /**
     * Format tanggal ke bahasa Indonesia
     */
    private function formatTanggalIndonesia(Carbon $date): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $dayName = $hari[$date->dayOfWeek];
        $day = $date->day;
        $month = $bulan[$date->month];
        $year = $date->year;
        
        return "$dayName, $day $month $year";
    }

    /**
     * Format tanggal singkat untuk tooltip
     */
    private function formatTanggalSingkat(Carbon $date): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                  'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $dayName = $hari[$date->dayOfWeek];
        $day = $date->day;
        $month = $bulan[$date->month];
        
        return "$dayName, $day $month";
    }

    /**
     * Format waktu ke format Indonesia (HH:mm WIB)
     */
    private function formatWaktuIndonesia(Carbon $date): string
    {
        return $date->format('H:i') . ' WIB';
    }

    /**
     * Hitung jumlah gelas dari ml
     */
    private function mlToGlasses(int $ml): int
    {
        return (int) floor($ml / self::ML_PER_GLASS);
    }

    /**
     * Hitung persentase target
     */
    private function calculatePercentage(int $ml): int
    {
        if (self::TARGET_ML_PER_DAY == 0) return 0;
        $percentage = ($ml / self::TARGET_ML_PER_DAY) * 100;
        return min(100, max(0, (int) round($percentage)));
    }

    /**
     * 🔹 Simpan konsumsi air (default 250ml per klik, max 2000ml per hari per user)
     */
    public function store(Request $request)
    {
        try {
            // Fix: Pakai AuthToken untuk konsistensi dengan controller lain
            [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan', 'admin']);

            $todayDate = Carbon::today('Asia/Jakarta')->toDateString();
            $jumlahBaru = (int) $request->input('jumlah_ml', self::ML_PER_GLASS);

            // Validasi batas konsumsi harian
            $waterIntake = WaterIntake::where('user_id', $uid)
                ->where('tanggal', $todayDate)
                ->first();

            if ($waterIntake) {
                if (($waterIntake->jumlah_ml + $jumlahBaru) > self::TARGET_ML_PER_DAY) {
                    $totalGelas = $this->mlToGlasses($waterIntake->jumlah_ml);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Batas konsumsi air hari ini telah tercapai',
                        'data' => [
                            'jumlah_ml' => $waterIntake->jumlah_ml,
                            'jumlah_gelas' => $totalGelas,
                            'target_gelas' => self::TARGET_GLASSES,
                            'target_tercapai' => $totalGelas >= self::TARGET_GLASSES,
                            'persentase' => $this->calculatePercentage($waterIntake->jumlah_ml),
                        ],
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
                    'user_id' => $uid,
                    'jumlah_ml' => $jumlahBaru,
                    'tanggal' => $todayDate,
                ]);
                $totalToday = $jumlahBaru;
            }

            // Hitung data lengkap untuk response
            $totalGelas = $this->mlToGlasses($totalToday);
            $sisaGelas = max(0, self::TARGET_GLASSES - $totalGelas);
            $targetTercapai = $totalGelas >= self::TARGET_GLASSES;
            $persentase = $this->calculatePercentage($totalToday);
            $now = Carbon::now('Asia/Jakarta');

            return response()->json([
                'status' => 'success',
                'message' => 'Konsumsi air berhasil disimpan',
                'data' => [
                    'id' => $entry->id,
                    'user_id' => $entry->user_id,
                    'jumlah_ml' => $totalToday,
                    'jumlah_gelas' => $totalGelas,
                    'sisa_gelas' => $sisaGelas,
                    'target_gelas' => self::TARGET_GLASSES,
                    'target_ml' => self::TARGET_ML_PER_DAY,
                    'target_tercapai' => $targetTercapai,
                    'persentase' => $persentase,
                    'tanggal' => $todayDate,
                    'last_updated_at' => $entry->updated_at->setTimezone('Asia/Jakarta')->toIso8601String(),
                    'last_updated_formatted' => $this->formatWaktuIndonesia($entry->updated_at->setTimezone('Asia/Jakarta')),
                ],
                'current_time' => $now->toIso8601String(),
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan data air', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 🔹 Tampilkan riwayat konsumsi air 7 hari terakhir (termasuk hari ini)
     */
    public function index(Request $request)
    {
        try {
            [$uid, $role] = AuthToken::ensureActiveAndFreshOrFail($request);

            $today = Carbon::today('Asia/Jakarta');
            $now = Carbon::now('Asia/Jakarta');
            $history = [];

            // Ambil data hari ini
            $todayData = WaterIntake::where('user_id', $uid)
                ->where('tanggal', $today->toDateString())
                ->first();

            $totalKonsumsiHariIni = $todayData ? $todayData->jumlah_ml : 0;
            $totalGelasHariIni = $this->mlToGlasses($totalKonsumsiHariIni);
            $targetTercapai = $totalGelasHariIni >= self::TARGET_GLASSES;
            $persentase = $this->calculatePercentage($totalKonsumsiHariIni);
            $lastUpdated = $todayData ? $todayData->updated_at->setTimezone('Asia/Jakarta') : $now;

            // Data hari ini lengkap
            $todayResponse = [
                'tanggal' => $today->toDateString(),
                'tanggal_formatted' => $this->formatTanggalIndonesia($today),
                'jumlah_ml' => $totalKonsumsiHariIni,
                'jumlah_gelas' => $totalGelasHariIni,
                'target_gelas' => self::TARGET_GLASSES,
                'target_ml' => self::TARGET_ML_PER_DAY,
                'target_tercapai' => $targetTercapai,
                'persentase' => $persentase,
                'last_updated_at' => $lastUpdated->toIso8601String(),
                'last_updated_formatted' => $this->formatWaktuIndonesia($lastUpdated),
            ];

            // Data 7 hari terakhir untuk chart (hanya mapping data, tanpa target)
            $totalMl7Hari = 0;

            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateString = $date->toDateString();
                
                $waterIntake = WaterIntake::where('user_id', $uid)
                    ->where('tanggal', $dateString)
                    ->first();

                $jumlahMl = $waterIntake ? $waterIntake->jumlah_ml : 0;
                $jumlahGelas = $this->mlToGlasses($jumlahMl);
                $totalMl7Hari += $jumlahMl;

                // Hanya mapping data konsumsi, tanpa konsep target
                $history[] = [
                    'tanggal' => $dateString,
                    'tanggal_label' => $date->format('d'), // "12", "13", "14" untuk label chart
                    'tanggal_formatted' => $this->formatTanggalSingkat($date), // "Rabu, 12 Des" untuk tooltip
                    'jumlah_ml' => $jumlahMl,
                    'jumlah_gelas' => $jumlahGelas,
                ];
            }

            // Statistik agregat (hanya rata-rata dan total, tanpa target tercapai)
            $rataRataGelas = count($history) > 0 ? round(array_sum(array_column($history, 'jumlah_gelas')) / count($history), 1) : 0;

            return response()->json([
                'status' => 'success',
                'message' => 'Data riwayat berhasil diambil',
                'current_time' => $now->toIso8601String(),
                'today' => $todayResponse,
                'history_7_hari' => $history,
                'statistik' => [
                    'rata_rata_gelas_7_hari' => $rataRataGelas,
                    'total_ml_7_hari' => $totalMl7Hari,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Gagal mengambil riwayat air', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
