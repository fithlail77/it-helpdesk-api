<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Sparepart::query();

            if ($request->filled('search')) {
                $query->where('name', 'like', "%{$request->search}%");
            }

            $spareparts = $query->orderBy('name')->paginate($request->get('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $spareparts,
                'message' => 'Daftar sparepart berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data sparepart',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
            ]);

            $sparepart = Sparepart::create($validated);

            return response()->json([
                'success' => true,
                'data' => $sparepart,
                'message' => 'Sparepart berhasil dibuat',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat sparepart',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $sparepart = Sparepart::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $sparepart,
                'message' => 'Detail sparepart berhasil diambil',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sparepart tidak ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sparepart = Sparepart::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'stock' => 'sometimes|required|integer|min:0',
                'price' => 'sometimes|required|numeric|min:0',
            ]);

            $sparepart->update($validated);

            return response()->json([
                'success' => true,
                'data' => $sparepart,
                'message' => 'Sparepart berhasil diupdate',
            ]);
        } catch (\Illuminate\Database\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sparepart tidak ditemukan',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $sparepart = Sparepart::findOrFail($id);
            $sparepart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil dihapus',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sparepart tidak ditemukan',
            ], 404);
        }
    }
}
