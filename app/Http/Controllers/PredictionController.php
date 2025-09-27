<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class PredictionController extends Controller
{
    private $flaskApiUrl;

    public function __construct()
    {
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://127.0.0.1:5001/predict');
    }

    // GET /api/predictions
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = Prediction::latest();
        $query->where('user_id', $user->id);

        if ($request->filled('method')) {
            $query->where('metode_persalinan', $request->method);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $predictions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $predictions
        ], 200);
    }

    // POST /api/predictions
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'usia_ibu' => 'required|integer|min:15|max:50',
            'tekanan_darah' => 'required|in:normal,rendah,tinggi',
            'riwayat_persalinan' => 'required|in:tidak ada,normal,caesar',
            'posisi_janin' => 'required|in:normal,lintang,sungsang',
            'riwayat_kesehatan_ibu' => 'nullable|string',
            'kondisi_kesehatan_janin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $dataToSend = [
                'usia_ibu' => (int) $request->input('usia_ibu'),
                'tekanan_darah' => strtolower($request->input('tekanan_darah')),
                'riwayat_persalinan' => strtolower($request->input('riwayat_persalinan')),
                'posisi_janin' => strtolower($request->input('posisi_janin')),
                'riwayat_kesehatan_ibu' => strtolower($request->input('riwayat_kesehatan_ibu') ?? 'normal'),
                'kondisi_kesehatan_janin' => strtolower($request->input('kondisi_kesehatan_janin') ?? 'normal'),
            ];

            $response = Http::timeout(10)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->flaskApiUrl, $dataToSend);

            if ($response->failed()) {
                $msg = "Gagal memanggil Flask API. Status: {$response->status()}. Respons: {$response->body()}";
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], $response->status());
            }

            $result = $response->json();

            $hasil = $result['hasil_prediksi'] ?? '-';
            $faktor = !empty($result['faktor']) ? (is_array($result['faktor']) ? implode(', ', $result['faktor']) : $result['faktor']) : '-';
            $confidence = !empty($result['confidence']) ? $result['confidence'] : 0;

            $prediction = Prediction::create([
                'usia_ibu' => $dataToSend['usia_ibu'],
                'tekanan_darah' => $dataToSend['tekanan_darah'],
                'riwayat_persalinan' => $dataToSend['riwayat_persalinan'],
                'posisi_janin' => $dataToSend['posisi_janin'],
                'riwayat_kesehatan_ibu' => $dataToSend['riwayat_kesehatan_ibu'],
                'kondisi_kesehatan_janin' => $dataToSend['kondisi_kesehatan_janin'],
                'metode_persalinan' => $hasil,
                'faktor' => $faktor,
                'confidence' => $confidence,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan prediksi.',
                'hasil_prediksi' => $hasil,
                'confidence' => $confidence,
                'faktor' => $faktor,
                'data' => $prediction
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat prediksi: ' . $e->getMessage()
            ], 500);
        }
    }


    // DELETE /api/predictions/{id}
    public function deletebyID($id, Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $prediction = Prediction::find($id);

        if (!$prediction) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($user->id !== $prediction->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $prediction->delete();

        return response()->json(['success' => true, 'message' => 'Riwayat prediksi berhasil dihapus']);
    }

    // GET /api/predictions/{id}
    public function result($id, Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $prediction = Prediction::find($id);

        if (!$prediction) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($user->id !== $prediction->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $prediction
        ]);
    }
}
