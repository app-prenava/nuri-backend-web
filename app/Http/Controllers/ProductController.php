<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Untuk validasi

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'message' => 'Daftar produk berhasil diambil.',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|url|max:2048', // Validasi sebagai URL
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan.',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product) // Menggunakan Route Model Binding
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil.',
            'data' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product) // Menggunakan Route Model Binding
    {
        $validator = Validator::make($request->all(), [
            'produk' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric|min:0',
            'gambar' => 'nullable|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui.',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product) // Menggunakan Route Model Binding
    {
        try {
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ], 200); // atau 204 No Content
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}