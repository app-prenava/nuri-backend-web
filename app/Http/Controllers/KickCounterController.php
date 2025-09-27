<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\KickCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class KickCounterController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kick_count' => 'required|integer|min:1',
                'duration' => 'required|integer|min:0',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);

            }
            
            $kickCounter = KickCounter::create([
                'user_id' => $user->id,
                'kick_count' => $request->kick_count,
                'recorded_at' => now(),
                'duration' => $request->duration,
            ]);
    
            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $kickCounter
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index()
    {
        $user = Auth::user();
        $kickCounter = KickCounter::where('user_id', $user->id)->get();
        
        return response()->json([
            'data' => $kickCounter
        ]);
    }
}
