<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Prediction;
use App\Models\User;

class PredictionDesktopController extends Controller
{
    // Daftar prediksi: filter HPL, Nama Ibu, Metode
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'bidan') {
            $allPredictions = Prediction::all();
            $users = User::where('role', 'ibu_hamil')->get();

            $query = Prediction::with(['user', 'hpl'])->latest();
            if ($request->filled('method')) {
                $query->where('metode_persalinan', $request->method);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('hpl')) {
                $query->whereHas('hpl', function ($q) use ($request) {
                    $q->whereDate('hpl', $request->hpl);
                });
            }
            // FILTER SEARCH NAMA
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }
            $predictions = $query->get();
        } else {
            $allPredictions = Prediction::where('user_id', $user->id)->get();
            $users = collect([$user]);

            $query = Prediction::with(['user', 'hpl'])->where('user_id', $user->id)->latest();
            if ($request->filled('method')) {
                $query->where('metode_persalinan', $request->method);
            }
            if ($request->filled('hpl')) {
                $query->whereHas('hpl', function ($q) use ($request) {
                    $q->whereDate('hpl', $request->hpl);
                });
            }
            // FILTER SEARCH NAMA (buat user, biasanya tidak perlu karena nama = dirinya sendiri, tapi boleh kalau mau konsisten)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }
            $predictions = $query->get();
        }

        return view('prediksi.index', [
            'predictions'      => $predictions,
            'users'            => $users,
            'allPredictions'   => $allPredictions,
        ]);
    }

    // Form input prediksi
    public function create()
    {
        $users = User::where('role', 'ibu_hamil')->get();
        return view('prediksi.form', compact('users'));
    }

    // Menampilkan detail hasil prediksi
    public function show($id)
    {
        $user = Auth::user();
        $prediction = Prediction::with(['user', 'hpl'])->findOrFail($id);

        // Validasi akses data
        if ($user->role !== 'bidan' && $user->id !== $prediction->user_id) {
            return redirect()->route('prediksi.index')->with('error', 'Anda tidak memiliki akses ke data ini');
        }

        return view('prediksi.result', compact('prediction'));
    }

    // Menampilkan halaman cetak hasil prediksi
    public function print($id)
    {
        $user = Auth::user();
        $prediction = Prediction::with(['user', 'hpl'])->findOrFail($id);

        // Validasi akses data
        if ($user->role !== 'bidan' && $user->id !== $prediction->user_id) {
            return redirect()->route('prediksi.index')->with('error', 'Anda tidak memiliki akses ke data ini');
        }

        return view('prediksi.print', compact('prediction'));
    }

    // Tampilkan data prediksi terbaru (latest)
    public function indexlatest()
    {
        $user = Auth::user();

        $prediction = $user->role === 'bidan'
            ? Prediction::with(['user', 'hpl'])->latest()->first()
            : Prediction::with(['user', 'hpl'])->where('user_id', $user->id)->latest()->first();

        if (!$prediction) {
            return redirect()->route('prediksi.index')->with('info', 'Belum ada data prediksi');
        }

        return redirect()->route('prediksi.show', ['id' => $prediction->id]);
    }

    // Proses simpan prediksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'usia_ibu' => 'required|integer|min:15|max:50',
            'tekanan_darah' => 'required|in:normal,rendah,tinggi',
            'riwayat_persalinan' => 'required|in:tidak ada,normal,caesar',
            'posisi_janin' => 'required|in:normal,lintang,sungsang',
            'riwayat_kesehatan_ibu' => ['required', 'string', 'regex:/[a-zA-Z]/'],
            'kondisi_kesehatan_janin' => ['required', 'string', 'regex:/[a-zA-Z]/'],
        ], [
            'riwayat_kesehatan_ibu.regex' => 'Riwayat kesehatan ibu tidak boleh hanya angka.',
            'kondisi_kesehatan_janin.regex' => 'Kondisi kesehatan janin tidak boleh hanya angka.',
        ]);

        try {
            $dataToSend = [
                'usia_ibu' => (int)$request->usia_ibu,
                'tekanan_darah' => strtolower($request->tekanan_darah),
                'riwayat_persalinan' => strtolower($request->riwayat_persalinan),
                'posisi_janin' => strtolower($request->posisi_janin),
                'riwayat_kesehatan_ibu' => strtolower($request->riwayat_kesehatan_ibu),
                'kondisi_kesehatan_janin' => strtolower($request->kondisi_kesehatan_janin),
            ];

            $response = Http::post('https://sehatimlprediksi-production.up.railway.app/predict', $dataToSend);
            $result = $response->json();

            if (!$response->ok() || !isset($result['hasil_prediksi'])) {
                return redirect()->back()->with('error', 'Prediksi gagal. Coba lagi.');
            }

            $prediction = Prediction::create([
                'user_id' => $request->user_id,
                'usia_ibu' => $dataToSend['usia_ibu'],
                'tekanan_darah' => $dataToSend['tekanan_darah'],
                'riwayat_persalinan' => $dataToSend['riwayat_persalinan'],
                'posisi_janin' => $dataToSend['posisi_janin'],
                'riwayat_kesehatan_ibu' => $dataToSend['riwayat_kesehatan_ibu'],
                'kondisi_kesehatan_janin' => $dataToSend['kondisi_kesehatan_janin'],
                'metode_persalinan' => $result['hasil_prediksi'] ?? '-',
                'faktor' => $result['faktor'] ?? '-',
                'confidence' => $result['confidence'] ?? 0,
            ]);

            return redirect()->route('prediksi.show', $prediction->id)
                ->with('success', 'Data prediksi berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat prediksi: ' . $e->getMessage());
        }
    }


    // Hapus semua data prediksi
    public function deleteAll()
    {
        $user = Auth::user();

        if ($user->role === 'bidan') {
            Prediction::truncate();
        } else {
            Prediction::where('user_id', $user->id)->delete();
        }

        return redirect()->route('prediksi.index')->with('success', 'Semua data prediksi berhasil dihapus');
    }

    // Hapus satu data prediksi
    public function destroy($id)
    {
        $user = Auth::user();
        $prediction = Prediction::findOrFail($id);

        // Validasi akses hapus
        if ($user->role !== 'bidan' && $user->id !== $prediction->user_id) {
            return redirect()->route('prediksi.index')->with('error', 'Anda tidak memiliki akses menghapus data ini');
        }

        $prediction->delete();
        return redirect()->route('prediksi.index')->with('success', 'Data prediksi berhasil dihapus');
    }
}
