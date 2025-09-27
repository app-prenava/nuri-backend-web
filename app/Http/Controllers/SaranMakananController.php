<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaranMakanan;

class SaranMakananContoller extends Controller
{
    public function index()
    {
        $SaranMakanan = SaranMakanan::all();

        return response()->json([
            'SaranMakanan' => $SaranMakanan
        ]);
    }

}
