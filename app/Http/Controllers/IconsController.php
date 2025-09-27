<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Icons; // Pastikan path ke model Icon sudah benar
use Illuminate\Http\Request;
use App\Http\Resources\IconResource; // Opsional, jika Anda memutuskan untuk menggunakannya
use Illuminate\Support\Facades\Log; // Untuk mencatat error ke log server

class IconsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Baris ini akan mencoba mengambil semua data dari model Icon
            $icons = Icons::all();

            // Jika Anda ingin menggunakan API Resource (disarankan untuk transformasi data yang konsisten):
            // return iconsResource::collection($iconss);

            // Jika tidak menggunakan API Resource:
            return response()->json([
                'success' => true,
                'data' => $icons,
                'message' => 'iconss retrieved successfully.'
            ]);

        } catch (\Throwable $e) { // Menangkap semua jenis error atau exception (\Throwable lebih luas dari \Exception)
            // 1. Catat error lengkap ke log server (ini sangat penting untuk debugging sisi server)
            Log::error('Error in iconsController@index: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString() // Memberikan stack trace lengkap di log
            ]);

            // 2. Persiapkan respons error untuk dikirim ke client
            $errorResponse = [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencoba mengambil data ikon.',
            ];

            // 3. Tambahkan detail error ke respons HANYA JIKA APP_DEBUG adalah true
            if (config('app.debug', false)) { // Default ke false jika tidak diset
                $errorResponse['error_details'] = [
                    'message' => $e->getMessage(),
                    'exception_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    // Anda bisa memilih untuk tidak menyertakan trace di respons JSON karena bisa sangat panjang
                    // 'trace' => $e->getTraceAsString(),
                ];
            }

            // 4. Kembalikan respons JSON dengan status code 500 (Internal Server Error)
            return response()->json($errorResponse, 500);
        }
    }
}