<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatatanKunjungan;

class CatatanController extends Controller
{
    public function index()
    {
        $CatatanKunjungan = CatatanKunjungan::all();

        return response()->json([
            'CatatanKunjungan' => $CatatanKunjungan
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_kunjungan' => 'required',
            'keluhan' => 'required',
            'pertanyaan' => 'required',
            'hasil_kunjungan' => 'required',
        ]);

        DeteksiPenyakit::create([
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'keluhan' => $request->keluhan,
            'bmi' => $request->bmi,
            'pertanyaan' => $request->pertanyaan,

        ]);
        return response()->json([
            'status' => 'berhasil',
            'message' => 'Status created successfully'
        ], 201);
    }

    public function deleteAll()
    {
        CatatanKunjungan::truncate(); 
        return response()->json([
            'status' => 'success',
            'message' => 'All data deleted successfully'
        ], 200);
    }

   
    public function deleteById($id)
    {
        $deteksi = CatatanKunjungan::find($id);

        if (!$deteksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found'
            ], 404);
        }

        $deteksi->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Data deleted successfully'
        ], 200);
    }
}
