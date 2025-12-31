<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PregnancyCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PregnancyCalculatorController extends Controller
{
    /**
     * 🔹 Kalkulator HPL Real-Time (Tanpa Save ke Database)
     * Untuk preview kalkulasi sebelum user menyimpan data
     */
    public function calculate(Request $request)
    {
        try {
            $request->validate([
                'hpht' => 'required|date|before_or_equal:today',
            ], [
                'hpht.before_or_equal' => 'HPHT tidak boleh lebih dari hari ini',
            ]);

            $hpht = Carbon::parse($request->hpht);
            $today = now('Asia/Jakarta');

            // Validasi: HPHT tidak boleh di masa depan
            if ($hpht->greaterThan($today)) {
                return response()->json([
                    'message' => 'HPHT tidak boleh lebih dari hari ini'
                ], 422);
            }

            // Hitung HPL dengan Rumus Naegele
            // HPHT + 7 hari
            // Bulan HPHT - 3 bulan
            // Tahun HPHT + 1 tahun
            $hpl = $hpht->copy()
                ->addDays(7)
                ->subMonthsNoOverflow(3)
                ->addYearNoOverflow();

            // Hitung data kehamilan lengkap
            $pregnancyData = $this->calculatePregnancyData($hpht, $hpl);

            return response()->json([
                'message' => 'Kalkulasi HPL berhasil',
                'data' => [
                    'hpht' => $hpht->toDateString(),
                    'hpl' => $hpl->toDateString(),
                    'hpl_formatted' => $hpl->format('d F Y'),
                    'usia_kehamilan' => $pregnancyData['usia_kehamilan'],
                    'trimester' => $pregnancyData['trimester'],
                    'trimester_name' => $this->getTrimesterName($pregnancyData['trimester']),
                    'progress_percentage' => $pregnancyData['progress_percentage'],
                    'countdown' => $pregnancyData['countdown'],
                    'status' => $pregnancyData['status'],
                    'status_description' => $this->getStatusDescription($pregnancyData['status']),
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Gagal mengalkulasi HPL:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengalkulasi HPL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Ambil data kehamilan user yang sedang login
     * Return data lengkap dengan kalkulasi real-time
     */
    public function getMyPregnancy(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Ambil data kehamilan terakhir user
            $pregnancy = PregnancyCalculator::where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$pregnancy) {
                return response()->json([
                    'message' => 'Data kehamilan belum diisi',
                    'data' => null
                ], 404);
            }

            $hpht = $pregnancy->hpht ? Carbon::parse($pregnancy->hpht) : null;
            $hpl = Carbon::parse($pregnancy->hpl);

            // Hitung data kehamilan lengkap
            $pregnancyData = $hpht
                ? $this->calculatePregnancyData($hpht, $hpl)
                : null;

            return response()->json([
                'message' => 'Data kehamilan berhasil diambil',
                'data' => [
                    'id' => $pregnancy->id,
                    'hpht' => $pregnancy->hpht,
                    'hpht_formatted' => $hpht ? $hpht->format('d F Y') : null,
                    'hpl' => $pregnancy->hpl,
                    'hpl_formatted' => $hpl->format('d F Y'),
                    'usia_kehamilan' => $pregnancyData['usia_kehamilan'] ?? null,
                    'trimester' => $pregnancyData['trimester'] ?? null,
                    'trimester_name' => $pregnancyData ? $this->getTrimesterName($pregnancyData['trimester']) : null,
                    'progress_percentage' => $pregnancyData['progress_percentage'] ?? null,
                    'countdown' => $pregnancyData['countdown'] ?? null,
                    'status' => $pregnancyData['status'] ?? null,
                    'status_description' => $pregnancyData ? $this->getStatusDescription($pregnancyData['status']) : null,
                    'created_at' => $pregnancy->created_at,
                    'updated_at' => $pregnancy->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Gagal mengambil data kehamilan:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Gagal mengambil data kehamilan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Update data kehamilan yang sudah ada
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'hpht' => 'nullable|date|before_or_equal:today',
                'hpl' => 'nullable|date',
            ], [
                'hpht.before_or_equal' => 'HPHT tidak boleh lebih dari hari ini',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $pregnancy = PregnancyCalculator::findOrFail($id);

            // Cek apakah user punya akses
            if ($pregnancy->user_id !== $user->user_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $hpht = $request->hpht ? Carbon::parse($request->hpht) : null;
            $hpl = $request->hpl ? Carbon::parse($request->hpl) : null;

            // Validasi: minimal salah satu harus diisi
            if (!$hpht && !$hpl) {
                return response()->json([
                    'message' => 'HPHT atau HPL wajib diisi!'
                ], 422);
            }

            // Update data
            if ($hpht) {
                $pregnancy->hpht = $hpht->toDateString();

                // Hitung ulang HPL jika HPHT diupdate
                if (!$hpl) {
                    $hplCalculated = $hpht->copy()
                        ->addDays(7)
                        ->subMonthsNoOverflow(3)
                        ->addYearNoOverflow();
                    $pregnancy->hpl = $hplCalculated->toDateString();
                }
            }

            if ($hpl) {
                $pregnancy->hpl = $hpl->toDateString();
            }

            $pregnancy->save();

            // Hitung data kehamilan lengkap untuk response
            $finalHpht = $pregnancy->hpht ? Carbon::parse($pregnancy->hpht) : null;
            $finalHpl = Carbon::parse($pregnancy->hpl);
            $pregnancyData = $finalHpht
                ? $this->calculatePregnancyData($finalHpht, $finalHpl)
                : null;

            return response()->json([
                'message' => 'Data kehamilan berhasil diupdate',
                'data' => [
                    'id' => $pregnancy->id,
                    'hpht' => $pregnancy->hpht,
                    'hpht_formatted' => $finalHpht ? $finalHpht->format('d F Y') : null,
                    'hpl' => $pregnancy->hpl,
                    'hpl_formatted' => $finalHpl->format('d F Y'),
                    'usia_kehamilan' => $pregnancyData['usia_kehamilan'] ?? null,
                    'trimester' => $pregnancyData['trimester'] ?? null,
                    'trimester_name' => $pregnancyData ? $this->getTrimesterName($pregnancyData['trimester']) : null,
                    'progress_percentage' => $pregnancyData['progress_percentage'] ?? null,
                    'countdown' => $pregnancyData['countdown'] ?? null,
                    'status' => $pregnancyData['status'] ?? null,
                    'status_description' => $pregnancyData ? $this->getStatusDescription($pregnancyData['status']) : null,
                    'updated_at' => $pregnancy->updated_at,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Gagal update data kehamilan:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Gagal mengupdate data kehamilan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Simpan data HPL berdasarkan HPHT user (UPDATED dengan Rumus Naegele yang benar)
     */
    public function store(Request $request)
    {
        try {
            // Validasi: salah satu wajib diisi
            $request->validate([
                'hpht' => 'nullable|date|before_or_equal:today',
                'hpl' => 'nullable|date',
            ], [
                'hpht.before_or_equal' => 'HPHT tidak boleh lebih dari hari ini',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $hpht = $request->hpht ? Carbon::parse($request->hpht) : null;
            $hpl = $request->hpl ? Carbon::parse($request->hpl) : null;

            // Jika keduanya kosong, return error
            if (!$hpht && !$hpl) {
                return response()->json(['message' => 'HPHT atau HPL wajib diisi!'], 422);
            }

            // Hitung HPL otomatis jika hanya HPHT diisi (RUMUS NAEGELE)
            if ($hpht && !$hpl) {
                // Rumus Naegele: HPHT + 7 hari, -3 bulan, +1 tahun
                $hpl = $hpht->copy()
                    ->addDays(7)
                    ->subMonthsNoOverflow(3)
                    ->addYearNoOverflow();
            }

            // Hitung data kehamilan lengkap
            $pregnancyData = $hpht
                ? $this->calculatePregnancyData($hpht, $hpl)
                : null;

            $data = PregnancyCalculator::create([
                'user_id' => $user->user_id,
                'hpht'    => $hpht?->toDateString(),
                'hpl'     => $hpl?->toDateString(),
            ]);

            return response()->json([
                'message' => 'Data kehamilan berhasil disimpan',
                'data' => [
                    'id' => $data->id,
                    'hpht' => $data->hpht,
                    'hpht_formatted' => $hpht ? $hpht->format('d F Y') : null,
                    'hpl' => $data->hpl,
                    'hpl_formatted' => $hpl ? $hpl->format('d F Y') : null,
                    'usia_kehamilan' => $pregnancyData['usia_kehamilan'] ?? null,
                    'trimester' => $pregnancyData['trimester'] ?? null,
                    'trimester_name' => $pregnancyData ? $this->getTrimesterName($pregnancyData['trimester']) : null,
                    'progress_percentage' => $pregnancyData['progress_percentage'] ?? null,
                    'countdown' => $pregnancyData['countdown'] ?? null,
                    'status' => $pregnancyData['status'] ?? null,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan HPL:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Simpan data HPL manual (input HPL langsung)
     */
    public function storeManual(Request $request)
    {
        try {
            $request->validate([
                'hpl' => 'required|date|after:today',
            ], [
                'hpl.after' => 'HPL harus lebih dari hari ini',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $hpl = Carbon::parse($request->hpl);

            $data = PregnancyCalculator::create([
                'user_id' => $user->user_id,
                'hpht'    => null, // Tidak diisi karena input manual
                'hpl'     => $hpl->toDateString(),
            ]);

            return response()->json([
                'message' => 'Data HPL manual berhasil disimpan',
                'data' => [
                    'id' => $data->id,
                    'hpht' => $data->hpht,
                    'hpl' => $data->hpl,
                    'hpl_formatted' => $hpl->format('d F Y'),
                    'usia_kehamilan' => null,
                    'trimester' => null,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Gagal simpan HPL manual:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat simpan data manual',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Ambil semua data kehamilan semua user (untuk bidan) - UPDATED
     */
    public function index()
    {
        try {
            $data = PregnancyCalculator::with('user')->orderBy('created_at', 'desc')->get();
            $today = now('Asia/Jakarta');

            $data = $data->map(function($item) use ($today) {
                $hpht = $item->hpht ? Carbon::parse($item->hpht) : null;
                $hpl = Carbon::parse($item->hpl);

                if ($hpht) {
                    $pregnancyData = $this->calculatePregnancyData($hpht, $hpl);
                    $item->usia_kehamilan = $pregnancyData['usia_kehamilan'];
                    $item->trimester = $pregnancyData['trimester'];
                    $item->progress_percentage = $pregnancyData['progress_percentage'];
                    $item->countdown = $pregnancyData['countdown'];
                    $item->status = $pregnancyData['status'];
                } else {
                    $item->usia_kehamilan = null;
                    $item->trimester = null;
                    $item->progress_percentage = null;
                    $item->countdown = null;
                    $item->status = null;
                }

                return $item;
            });

            return response()->json([
                'message' => 'Data berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Gagal mengambil data kehamilan:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Tampilkan detail berdasarkan ID - UPDATED
     */
    public function show($id)
    {
        try {
            $data = PregnancyCalculator::with('user')->findOrFail($id);
            $hpht = $data->hpht ? Carbon::parse($data->hpht) : null;
            $hpl = Carbon::parse($data->hpl);

            $pregnancyData = $hpht
                ? $this->calculatePregnancyData($hpht, $hpl)
                : null;

            $responseData = [
                'id' => $data->id,
                'user' => $data->user,
                'hpht' => $data->hpht,
                'hpht_formatted' => $hpht ? $hpht->format('d F Y') : null,
                'hpl' => $data->hpl,
                'hpl_formatted' => $hpl->format('d F Y'),
                'usia_kehamilan' => $pregnancyData['usia_kehamilan'] ?? null,
                'trimester' => $pregnancyData['trimester'] ?? null,
                'trimester_name' => $pregnancyData ? $this->getTrimesterName($pregnancyData['trimester']) : null,
                'progress_percentage' => $pregnancyData['progress_percentage'] ?? null,
                'countdown' => $pregnancyData['countdown'] ?? null,
                'status' => $pregnancyData['status'] ?? null,
                'status_description' => $pregnancyData ? $this->getStatusDescription($pregnancyData['status']) : null,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

            return response()->json([
                'message' => 'Detail data berhasil diambil',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Gagal menampilkan detail HPL:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * 🔹 Hapus data berdasarkan ID (hanya oleh pemilik)
     */
    public function destroy($id)
    {
        try {
            $data = PregnancyCalculator::findOrFail($id);

            if ($data->user_id !== Auth::id()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $data->delete();

            return response()->json([
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Gagal menghapus data kehamilan:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Helper: Hitung data kehamilan lengkap
     */
    private function calculatePregnancyData($hpht, $hpl)
    {
        $today = now('Asia/Jakarta');

        // Hitung selisih hari dari HPHT ke hari ini
        $daysSinceHPHT = $hpht->diffInDays($today);

        // Hitung minggu dan hari
        $weeks = floor($daysSinceHPHT / 7);
        $days = $daysSinceHPHT % 7;

        // Tentukan trimester
        if ($weeks <= 13) {
            $trimester = 1;
        } elseif ($weeks <= 27) {
            $trimester = 2;
        } else {
            $trimester = 3;
        }

        // Hitung progress percentage (0-40 minggu = 280 hari)
        $progress = min(($daysSinceHPHT / 280) * 100, 100);

        // Countdown ke HPL
        $daysUntilHPL = $today->diffInDays($hpl, false);

        // Tentukan status
        if ($daysUntilHPL > 14) {
            $status = 'normal';
        } elseif ($daysUntilHPL >= 0) {
            $status = 'approaching';
        } elseif ($daysUntilHPL >= -7) {
            $status = 'overdue';
        } else {
            $status = 'critical';
        }

        return [
            'usia_kehamilan' => [
                'minggu' => (int)$weeks,
                'hari' => (int)$days,
                'total_hari' => $daysSinceHPHT,
                'teks' => (int)$weeks . ' Minggu ' . (int)$days . ' Hari',
            ],
            'trimester' => $trimester,
            'progress_percentage' => round($progress, 2),
            'countdown' => [
                'hari_sampai_hpl' => $daysUntilHPL,
                'minggu_sampai_hpl' => floor($daysUntilHPL / 7),
                'teks' => $daysUntilHPL > 0
                    ? abs(floor($daysUntilHPL / 7)) . ' minggu lagi'
                    : ($daysUntilHPL == 0 ? 'Hari ini' : abs($daysUntilHPL) . ' hari lewat HPL'),
            ],
            'status' => $status,
        ];
    }

    /**
     * 🔹 Helper: Nama trimester dalam Bahasa Indonesia
     */
    private function getTrimesterName($trimester)
    {
        $names = [
            1 => 'Trimester 1 (0-13 Minggu)',
            2 => 'Trimester 2 (14-27 Minggu)',
            3 => 'Trimester 3 (28-40 Minggu)',
        ];

        return $names[$trimester] ?? 'Unknown';
    }

    /**
     * 🔹 Helper: Deskripsi status dalam Bahasa Indonesia
     */
    private function getStatusDescription($status)
    {
        $descriptions = [
            'normal' => 'Kehamilan berjalan normal',
            'approaching' => 'Mendekati hari perkiraan lahir',
            'overdue' => 'Melewati hari perkiraan lahir',
            'critical' => 'Segera hubungi bidan/dokter',
        ];

        return $descriptions[$status] ?? 'Status tidak diketahui';
    }
}
