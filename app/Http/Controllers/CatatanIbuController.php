<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\CatatanIbu;
use App\Support\AuthToken;
use Illuminate\Support\Facades\DB;

class CatatanIbuController extends Controller
{
    /**
     * 🔹 Create catatan kunjungan baru (oleh ibu hamil atau bidan)
     */
    public function store(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $validator = Validator::make($request->all(), [
            'tanggal_kunjungan' => ['required', 'date'],
            'q1_demam' => ['nullable', 'boolean'],
            'q2_pusing' => ['nullable', 'boolean'],
            'q3_sulit_tidur' => ['nullable', 'boolean'],
            'q4_risiko_tb' => ['nullable', 'boolean'],
            'q5_gerakan_bayi' => ['nullable', 'boolean'],
            'q6_nyeri_perut' => ['nullable', 'boolean'],
            'q7_cairan_jalan_lahir' => ['nullable', 'boolean'],
            'q8_sakit_kencing' => ['nullable', 'boolean'],
            'q9_diare' => ['nullable', 'boolean'],
            'hasil_kunjungan' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Jika role ibu_hamil, gunakan user_id sendiri
        // Jika role bidan, bisa kirim user_id target (opsional)
        $userId = $uid;

        if ($role === 'bidan' && $request->has('user_id')) {
            $userId = $request->input('user_id');
        }

        $catatan = CatatanIbu::create([
            'user_id' => $userId,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'status_kunjungan' => 'sedang_berlangsung',
            'q1_demam' => $request->q1_demam,
            'q2_pusing' => $request->q2_pusing,
            'q3_sulit_tidur' => $request->q3_sulit_tidur,
            'q4_risiko_tb' => $request->q4_risiko_tb,
            'q5_gerakan_bayi' => $request->q5_gerakan_bayi,
            'q6_nyeri_perut' => $request->q6_nyeri_perut,
            'q7_cairan_jalan_lahir' => $request->q7_cairan_jalan_lahir,
            'q8_sakit_kencing' => $request->q8_sakit_kencing,
            'q9_diare' => $request->q9_diare,
            'hasil_kunjungan' => $request->hasil_kunjungan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan kunjungan berhasil dibuat',
            'data' => $this->formatResponse($catatan)
        ], 201);
    }

    /**
     * 🔹 Get semua catatan untuk user yang sedang login (ibu hamil)
     * atau semua catatan (untuk bidan)
     */
    public function index(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        if ($role === 'ibu_hamil') {
            // Ibu hamil hanya bisa lihat catatan sendiri
            $catatan = CatatanIbu::where('user_id', $uid)
                ->orderBy('tanggal_kunjungan', 'desc')
                ->get();
        } else {
            // Bidan bisa lihat semua catatan
            $catatan = CatatanIbu::with('user')
                ->orderBy('tanggal_kunjungan', 'desc')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data catatan berhasil diambil',
            'data' => $catatan->map(function ($item) {
                return $this->formatResponse($item);
            })
        ]);
    }

    /**
     * 🔹 Get detail catatan by ID
     */
    public function show(Request $request, int $catatanId): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $catatan = CatatanIbu::with('user')->find($catatanId);

        if (!$catatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Catatan tidak ditemukan'
            ], 404);
        }

        // Jika role ibu_hamil, hanya bisa lihat catatan sendiri
        if ($role === 'ibu_hamil' && $catatan->user_id !== $uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail catatan berhasil diambil',
            'data' => $this->formatResponse($catatan)
        ]);
    }

    /**
     * 🔹 Update catatan (bisa oleh ibu hamil atau bidan)
     */
    public function update(Request $request, int $catatanId): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $catatan = CatatanIbu::find($catatanId);

        if (!$catatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Catatan tidak ditemukan'
            ], 404);
        }

        // Jika role ibu_hamil, hanya bisa update catatan sendiri
        if ($role === 'ibu_hamil' && $catatan->user_id !== $uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_kunjungan' => ['sometimes', 'required', 'date'],
            'status_kunjungan' => ['sometimes', 'in:sedang_berlangsung,selesai'],
            'q1_demam' => ['nullable', 'boolean'],
            'q2_pusing' => ['nullable', 'boolean'],
            'q3_sulit_tidur' => ['nullable', 'boolean'],
            'q4_risiko_tb' => ['nullable', 'boolean'],
            'q5_gerakan_bayi' => ['nullable', 'boolean'],
            'q6_nyeri_perut' => ['nullable', 'boolean'],
            'q7_cairan_jalan_lahir' => ['nullable', 'boolean'],
            'q8_sakit_kencing' => ['nullable', 'boolean'],
            'q9_diare' => ['nullable', 'boolean'],
            'hasil_kunjungan' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update data
        if ($request->has('tanggal_kunjungan')) {
            $catatan->tanggal_kunjungan = $request->tanggal_kunjungan;
        }

        if ($request->has('status_kunjungan')) {
            $catatan->status_kunjungan = $request->status_kunjungan;
        }

        // Update jawaban pertanyaan jika ada
        $catatan->q1_demam = $request->q1_demam ?? $catatan->q1_demam;
        $catatan->q2_pusing = $request->q2_pusing ?? $catatan->q2_pusing;
        $catatan->q3_sulit_tidur = $request->q3_sulit_tidur ?? $catatan->q3_sulit_tidur;
        $catatan->q4_risiko_tb = $request->q4_risiko_tb ?? $catatan->q4_risiko_tb;
        $catatan->q5_gerakan_bayi = $request->q5_gerakan_bayi ?? $catatan->q5_gerakan_bayi;
        $catatan->q6_nyeri_perut = $request->q6_nyeri_perut ?? $catatan->q6_nyeri_perut;
        $catatan->q7_cairan_jalan_lahir = $request->q7_cairan_jalan_lahir ?? $catatan->q7_cairan_jalan_lahir;
        $catatan->q8_sakit_kencing = $request->q8_sakit_kencing ?? $catatan->q8_sakit_kencing;
        $catatan->q9_diare = $request->q9_diare ?? $catatan->q9_diare;

        // Update hasil kunjungan (hanya bidan)
        if ($role === 'bidan' && $request->has('hasil_kunjungan')) {
            $catatan->hasil_kunjungan = $request->hasil_kunjungan;
        }

        $catatan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan berhasil diupdate',
            'data' => $this->formatResponse($catatan)
        ]);
    }

    /**
     * 🔹 Delete catatan
     */
    public function destroy(Request $request, int $catatanId): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $catatan = CatatanIbu::find($catatanId);

        if (!$catatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Catatan tidak ditemukan'
            ], 404);
        }

        // Jika role ibu_hamil, hanya bisa delete catatan sendiri
        if ($role === 'ibu_hamil' && $catatan->user_id !== $uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }

        $catatan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan berhasil dihapus'
        ]);
    }

    /**
     * 🔹 Format response untuk frontend
     */
    private function formatResponse($catatan): array
    {
        return [
            'catatan_id' => $catatan->catatan_id,
            'user_id' => $catatan->user_id,
            'user' => $catatan->user ?? null,
            'tanggal_kunjungan' => $catatan->tanggal_kunjungan->format('Y-m-d'),
            'status_kunjungan' => $catatan->status_kunjungan,
            'pertanyaan' => [
                [
                    'id' => 'q1_demam',
                    'pertanyaan' => 'Demam lebih dari 2 hari',
                    'jawaban' => $catatan->q1_demam, // true = ya, false = tidak, null = belum dijawab
                ],
                [
                    'id' => 'q2_pusing',
                    'pertanyaan' => 'Pusing/sakit kepala berat',
                    'jawaban' => $catatan->q2_pusing,
                ],
                [
                    'id' => 'q3_sulit_tidur',
                    'pertanyaan' => 'Sulit tidur/cemas berlebih',
                    'jawaban' => $catatan->q3_sulit_tidur,
                ],
                [
                    'id' => 'q4_risiko_tb',
                    'pertanyaan' => 'Risiko TB batuk lebih dari 2 minggu atau kontak serumah dengan penderita TB',
                    'jawaban' => $catatan->q4_risiko_tb,
                ],
                [
                    'id' => 'q5_gerakan_bayi',
                    'pertanyaan' => 'Gerakan bayi Tidak ada atau Kurang dari 10x dalam 12 jam setelah minggu ke-24',
                    'jawaban' => $catatan->q5_gerakan_bayi,
                ],
                [
                    'id' => 'q6_nyeri_perut',
                    'pertanyaan' => 'Nyeri perut hebat',
                    'jawaban' => $catatan->q6_nyeri_perut,
                ],
                [
                    'id' => 'q7_cairan_jalan_lahir',
                    'pertanyaan' => 'Keluar cairan dari jalan lahir sangat banyak atau berbau',
                    'jawaban' => $catatan->q7_cairan_jalan_lahir,
                ],
                [
                    'id' => 'q8_sakit_kencing',
                    'pertanyaan' => 'Sakit saat kencing Atau keluar keputihan atau gatal di daerah kemaluan',
                    'jawaban' => $catatan->q8_sakit_kencing,
                ],
                [
                    'id' => 'q9_diare',
                    'pertanyaan' => 'Diare berulang',
                    'jawaban' => $catatan->q9_diare,
                ],
            ],
            'hasil_kunjungan' => $catatan->hasil_kunjungan,
            'created_at' => $catatan->created_at,
            'updated_at' => $catatan->updated_at,
        ];
    }
}
