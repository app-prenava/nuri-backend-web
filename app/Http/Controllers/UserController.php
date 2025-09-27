<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Icons; 
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
        public function getUserData()
    {
        try {
            // Ambil ID user dari token JWT
            $userId = auth()->id();

            if (!$userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Ambil data user dari database
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            return response()->json([
                'message' => 'Data user berhasil diambil',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getUserData: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function isidata(Request $request)
    {   
        try {
            // Get user from JWT token
            $userId = auth()->id();
            
            if (!$userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // Log the incoming request for debugging
            Log::info('Request data:', $request->all());
            
            // Validasi data yang masuk (tanpa validasi user_id)
            $validator = Validator::make($request->all(), [
                'tanggal_lahir' => 'nullable|date',
                'usia' => 'nullable|integer',
                'alamat' => 'nullable|string',
                'nomor_telepon' => 'nullable|string|max:20',
                'pendidikan_terakhir' => 'nullable|string|max:255',
                'pekerjaan' => 'nullable|string|max:255',
                'golongan_darah' => 'nullable|string|max:5',
                'nama_suami' => 'nullable|string|max:255',
                'telepon_suami' => 'nullable|string|max:20',
                'usia_suami' => 'nullable|integer',
                'pekerjaan_suami' => 'nullable|string|max:255',
                'usia_kehamilan' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            // Cari user berdasarkan ID dari token JWT
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            // Log yang akan diupdate
            Log::info('Updating user ID: ' . $userId);
            
            // Update data user yang sudah ada
            $user->update([
                'tanggal_lahir' => $request->tanggal_lahir,
                'usia' => $request->usia,
                'alamat' => $request->alamat,
                'nomor_telepon' => $request->nomor_telepon,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'pekerjaan' => $request->pekerjaan,
                'golongan_darah' => $request->golongan_darah,
                'nama_suami' => $request->nama_suami,
                'telepon_suami' => $request->telepon_suami,
                'usia_suami' => $request->usia_suami,
                'pekerjaan_suami' => $request->pekerjaan_suami,
                'usia_kehamilan' => $request->usia_kehamilan,
            ]);

            return response()->json([
                'message' => 'Data ibu hamil berhasil diperbarui!',
                'data' => $user
            ], 200);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in isidata: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        public function updateData(Request $request, $id)
    {
        try {
            // Validasi data
            $validator = Validator::make($request->all(), [
                'tanggal_lahir' => 'nullable|date',
                'usia' => 'nullable|integer',
                'alamat' => 'nullable|string',
                'nomor_telepon' => 'nullable|string|max:20',
                'pendidikan_terakhir' => 'nullable|string|max:255',
                'pekerjaan' => 'nullable|string|max:255',
                'golongan_darah' => 'nullable|string|max:5',
                'nama_suami' => 'nullable|string|max:255',
                'telepon_suami' => 'nullable|string|max:20',
                'usia_suami' => 'nullable|integer',
                'pekerjaan_suami' => 'nullable|string|max:255',
                'usia_kehamilan' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            // Cari user berdasarkan ID dari parameter
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            // Update data user
            $user->update([
                'tanggal_lahir' => $request->tanggal_lahir,
                'usia' => $request->usia,
                'alamat' => $request->alamat,
                'nomor_telepon' => $request->nomor_telepon,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'pekerjaan' => $request->pekerjaan,
                'golongan_darah' => $request->golongan_darah,
                'nama_suami' => $request->nama_suami,
                'telepon_suami' => $request->telepon_suami,
                'usia_suami' => $request->usia_suami,
                'pekerjaan_suami' => $request->pekerjaan_suami,
                'usia_kehamilan' => $request->usia_kehamilan,
            ]);

            return response()->json([
                'message' => 'Data user berhasil diperbarui!',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in updateData: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSelectedIcon(Request $request)
    {
        // 1. Ubah validasi untuk mengharapkan 'icon_id'
        //    Aturan 'exists:icons,id' tetap benar, karena ini mengecek nilai dari 'icon_id'
        //    terhadap kolom 'id' di tabel 'icons'.
        $request->validate([
            'icon_id' => 'required|integer|exists:icons,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        // 2. Ambil ID ikon dari request menggunakan key 'icon_id'
        $iconId = $request->icon_id; // atau $request->input('icon_id');

        // Set foreign key
        $user->selected_icon_id = $iconId;

        // Ambil data dari ikon yang dipilih
        // Pastikan nama modelnya benar (Icon, bukan Icons jika filenya Icon.php)
        $selectedIconData = Icons::find($iconId);

        // (Opsional: Bagian untuk menyimpan cache data ikon ke tabel user)
        // Jika Anda sudah menambahkan kolom selected_icon_name_cache dan selected_icon_data_cache di tabel users
        if ($selectedIconData) {
            $user->selected_icon_name_cache = $selectedIconData->name; // Asumsi ada kolom 'name' di model Icon
            $user->selected_icon_data_cache = $selectedIconData->data; // Asumsi ada kolom 'data' di model Icon
        } else {
            $user->selected_icon_name_cache = null;
            $user->selected_icon_data_cache = null;
        }
        // (Akhir bagian opsional)

        $user->save(); // Menyimpan selected_icon_id (dan kolom cache jika ada)

        $user->load('selectedIcon');

        return response()->json([
            'success' => true,
            'message' => 'Icon selected successfully.',
            'data' => $user
        ]);
    }
    /**
     * Get the authenticated user's profile including the selected icon.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        // Load relasi selectedIcon untuk disertakan dalam response
        $user->load('selectedIcon');

        // Jika menggunakan API Resource untuk User:
        // return new UserResource($user);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}