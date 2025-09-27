<?php

namespace App\Http\Controllers;

use App\Models\RekomendasiMakanan;
use Illuminate\Http\Request;

class RekomendasiMakananController extends Controller
{
    public function index()
    {
        // Ambil semua rekomendasi makanan
        $rekomendasi = RekomendasiMakanan::all();

        return response()->json([
            'status' => 'success',
            'data' => $rekomendasi
        ]);
    }

    public function show($id)
    {
        // Ambil rekomendasi makanan berdasarkan ID
        $rekomendasi = RekomendasiMakanan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $rekomendasi
        ]);
    }
}
