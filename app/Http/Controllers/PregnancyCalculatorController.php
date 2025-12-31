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

            // Ambil data ukuran janin
            $fetalSize = $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu']);

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
                    'ukuran_janin' => $fetalSize,
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

            // Ambil data ukuran janin
            $fetalSize = $pregnancyData
                ? $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu'])
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
                    'ukuran_janin' => $fetalSize,
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

            // Ambil data ukuran janin
            $fetalSize = $pregnancyData
                ? $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu'])
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
                    'ukuran_janin' => $fetalSize,
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

            // Ambil data ukuran janin
            $fetalSize = $pregnancyData
                ? $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu'])
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
                    'ukuran_janin' => $fetalSize,
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

            $data = $data->map(function($item) {
                $hpht = $item->hpht ? Carbon::parse($item->hpht) : null;
                $hpl = Carbon::parse($item->hpl);

                if ($hpht) {
                    $pregnancyData = $this->calculatePregnancyData($hpht, $hpl);
                    $item->usia_kehamilan = $pregnancyData['usia_kehamilan'];
                    $item->trimester = $pregnancyData['trimester'];
                    $item->progress_percentage = $pregnancyData['progress_percentage'];
                    $item->countdown = $pregnancyData['countdown'];
                    $item->status = $pregnancyData['status'];
                    $item->ukuran_janin = $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu']);
                } else {
                    $item->usia_kehamilan = null;
                    $item->trimester = null;
                    $item->progress_percentage = null;
                    $item->countdown = null;
                    $item->status = null;
                    $item->ukuran_janin = null;
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

            // Ambil data ukuran janin
            $fetalSize = $pregnancyData
                ? $this->getFetalSize($pregnancyData['usia_kehamilan']['minggu'])
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
                'ukuran_janin' => $fetalSize,
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

    /**
     * 🔹 Helper: Data ukuran janin per minggu kehamilan
     * Menggunakan perbandingan dengan buah/sayuran untuk visualisasi
     */
    private function getFetalSize($weeks)
    {
        $sizes = [
            4 => ['nama' => 'Poppy seed', 'nama_indo' => 'Biji poppy', 'berat_gr' => 1, 'panjang_cm' => 3],
            5 => ['nama' => 'Sesame seed', 'nama_indo' => 'Biji wijen', 'berat_gr' => 1, 'panjang_cm' => 4],
            6 => ['nama' => 'Lentil', 'nama_indo' => 'Kacang hijau', 'berat_gr' => 2, 'panjang_cm' => 5],
            7 => ['nama' => 'Blueberry', 'nama_indo' => 'Buah berry', 'berat_gr' => 3, 'panjang_cm' => 6],
            8 => ['nama' => 'Kidney bean', 'nama_indo' => 'Kacang merah', 'berat_gr' => 5, 'panjang_cm' => 7],
            9 => ['nama' => 'Grape', 'nama_indo' => 'Anggur', 'berat_gr' => 7, 'panjang_cm' => 8],
            10 => ['nama' => 'Kumquat', 'nama_indo' => 'Kuat', 'berat_gr' => 10, 'panjang_cm' => 9],
            11 => ['nama' => 'Fig', 'nama_indo' => 'Buah ara', 'berat_gr' => 14, 'panjang_cm' => 10],
            12 => ['nama' => 'Lemon', 'nama_indo' => 'Lemon', 'berat_gr' => 50, 'panjang_cm' => 8.5],
            13 => ['nama' => 'Pea pod', 'nama_indo' => 'Polong', 'berat_gr' => 70, 'panjang_cm' => 11],
            14 => ['nama' => 'Peach', 'nama_indo' => 'Peach', 'berat_gr' => 100, 'panjang_cm' => 12],
            15 => ['nama' => 'Apple', 'nama_indo' => 'Apel', 'berat_gr' => 140, 'panjang_cm' => 13],
            16 => ['nama' => 'Avocado', 'nama_indo' => 'Alpukat', 'berat_gr' => 180, 'panjang_cm' => 14],
            17 => ['nama' => 'Turnip', 'nama_indo' => ' Lobak', 'berat_gr' => 220, 'panjang_cm' => 15],
            18 => ['nama' => 'Bell pepper', 'nama_indo' => 'Paprika', 'berat_gr' => 280, 'panjang_cm' => 16],
            19 => ['nama' => 'Heirloom tomato', 'nama_indo' => 'Tomat', 'berat_gr' => 340, 'panjang_cm' => 17],
            20 => ['nama' => 'Banana', 'nama_indo' => 'Pisang', 'berat_gr' => 400, 'panjang_cm' => 18],
            21 => ['nama' => 'Carrot', 'nama_indo' => 'Wortel', 'berat_gr' => 470, 'panjang_cm' => 19],
            22 => ['nama' => 'Spaghetti squash', 'nama_indo' => 'Labu spaghetti', 'berat_gr' => 540, 'panjang_cm' => 20],
            23 => ['nama' => 'Large mango', 'nama_indo' => 'Mangga besar', 'berat_gr' => 620, 'panjang_cm' => 21],
            24 => ['nama' => 'Corn', 'nama_indo' => 'Jagung', 'berat_gr' => 700, 'panjang_cm' => 22],
            25 => ['nama' => 'Rutabaga', 'nama_indo' => ' Lobak Swedia', 'berat_gr' => 780, 'panjang_cm' => 23],
            26 => ['nama' => 'Scallion', 'nama_indo' => 'Daun bawang', 'berat_gr' => 860, 'panjang_cm' => 24],
            27 => ['nama' => 'Cauliflower', 'nama_indo' => 'Bunga kol', 'berat_gr' => 950, 'panjang_cm' => 25],
            28 => ['nama' => 'Eggplant', 'nama_indo' => 'Terong', 'berat_gr' => 1050, 'panjang_cm' => 26],
            29 => ['nama' => 'Butternut squash', 'nama_indo' => 'Labu butternut', 'berat_gr' => 1150, 'panjang_cm' => 27],
            30 => ['nama' => 'Cabbage', 'nama_indo' => 'Kol', 'berat_gr' => 1260, 'panjang_cm' => 28],
            31 => ['nama' => 'Coconut', 'nama_indo' => 'Kelapa', 'berat_gr' => 1370, 'panjang_cm' => 29],
            32 => ['nama' => 'Jicama', 'nama_indo' => 'Bengkuang', 'berat_gr' => 1500, 'panjang_cm' => 30],
            33 => ['nama' => 'Pineapple', 'nama_indo' => 'Nanas', 'berat_gr' => 1700, 'panjang_cm' => 31],
            34 => ['nama' => 'Cantaloupe', 'nama_indo' => 'Cantaloupe', 'berat_gr' => 1900, 'panjang_cm' => 32],
            35 => ['nama' => 'Honeydew melon', 'nama_indo' => 'Melon madu', 'berat_gr' => 2100, 'panjang_cm' => 33],
            36 => ['nama' => 'Papaya', 'nama_indo' => 'Pepaya', 'berat_gr' => 2300, 'panjang_cm' => 34],
            37 => ['nama' => 'Winter melon', 'nama_indo' => 'Labu musim dingin', 'berat_gr' => 2500, 'panjang_cm' => 35],
            38 => ['nama' => 'Pumpkin', 'nama_indo' => 'Labu kuning', 'berat_gr' => 2700, 'panjang_cm' => 36],
            39 => ['nama' => 'Watermelon', 'nama_indo' => 'Semangka kecil', 'berat_gr' => 2900, 'panjang_cm' => 37],
            40 => ['nama' => 'Jackfruit', 'nama_indo' => 'Nangka', 'berat_gr' => 3100, 'panjang_cm' => 38],
        ];

        // Return data ukuran janin sesuai minggu, atau null jika tidak ada data
        return $sizes[$weeks] ?? null;
    }
}
