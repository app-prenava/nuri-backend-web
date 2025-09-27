<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrediksiDepresi;

class PrediksiDepresiDesktopController extends Controller
{
    public function index(Request $request)
    {
        $query = PrediksiDepresi::with(['user', 'epds'])
                                ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $tanggal = $request->tanggal;
            $query->whereDate('created_at', $tanggal);
        }

        // Filter berdasarkan hasil - konsisten dengan logika di view
        if ($request->filled('hasil')) {
            if ($request->hasil == 'bergejala') {
                // Filter untuk yang bergejala depresi (Resiko Tinggi + Kemungkinan Gejala)
                $query->where(function($q) {
                    // Kondisi 1: hasil_prediksi = 1 AND (EPDS >= 12 OR tidak ada EPDS)
                    $q->where(function($subQuery1) {
                        $subQuery1->where('hasil_prediksi', 1)
                                  ->where(function($epdsCheck) {
                                      $epdsCheck->whereHas('epds', function($epdsQuery) {
                                          $epdsQuery->where('score', '>=', 12);
                                      })
                                      ->orWhereDoesntHave('epds');
                                  });
                    })
                    // Kondisi 2: hasil_prediksi = 0 BUT skor EPDS >= 12
                    ->orWhere(function($subQuery2) {
                        $subQuery2->where('hasil_prediksi', 0)
                                  ->whereHas('epds', function($epdsQuery) {
                                      $epdsQuery->where('score', '>=', 12);
                                  });
                    });
                });
            } elseif ($request->hasil == 'tidak_bergejala') {
                // Filter untuk yang tidak bergejala depresi
                $query->where(function($q) {
                    // Kondisi 1: hasil_prediksi = 1 BUT skor EPDS < 12
                    $q->where(function($subQuery1) {
                        $subQuery1->where('hasil_prediksi', 1)
                                  ->whereHas('epds', function($epdsQuery) {
                                      $epdsQuery->where('score', '<', 12);
                                  });
                    })
                    // Kondisi 2: hasil_prediksi = 0 AND (tidak ada EPDS OR skor EPDS < 12)
                    ->orWhere(function($subQuery2) {
                        $subQuery2->where('hasil_prediksi', 0)
                                  ->where(function($epdsCheck) {
                                      $epdsCheck->whereDoesntHave('epds')
                                                ->orWhereHas('epds', function($epdsQuery) {
                                                    $epdsQuery->where('score', '<', 12);
                                                });
                                  });
                    });
                });
            }
        }

        $prediksiList = $query->paginate(10);

        return view('depresi.index', compact('prediksiList'));
    }

    public function show($id)
    {
        $prediksi = PrediksiDepresi::with(['user', 'epds'])->findOrFail($id);
        return view('depresi.show', compact('prediksi'));
    }

    public function destroy($id)
    {
        $prediksi = PrediksiDepresi::find($id);
                
        if (!$prediksi) {
            return redirect()->route('depresi.index')
                             ->with('error', 'Data tidak ditemukan');
        }

        $prediksi->delete();

        return redirect()->route('depresi.index')
                         ->with('success', 'History prediksi berhasil dihapus');
    }
}