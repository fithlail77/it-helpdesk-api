<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IpDevice;
use Illuminate\Http\Request;

class IpDeviceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = IpDevice::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            }

            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }

            $perPage = $request->get('per_page', 15);
            $devices = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $devices,
                'message' => 'Daftar IP device berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data IP device',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'specifications' => 'nullable|string|max:500',
                'ip_address' => 'required|string|max:45|unique:ip_devices,ip_address',
                'location' => 'required|string|max:255',
            ]);

            $device = IpDevice::create($validated);

            return response()->json([
                'success' => true,
                'data' => $device,
                'message' => 'IP device berhasil dibuat',
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
                'message' => 'Terjadi kesalahan saat membuat IP device',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $device = IpDevice::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $device,
                'message' => 'Detail IP device berhasil diambil',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'IP device tidak ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $device = IpDevice::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'brand' => 'sometimes|required|string|max:255',
                'specifications' => 'nullable|string|max:500',
                'ip_address' => 'sometimes|required|string|max:45|unique:ip_devices,ip_address,' . $id,
                'location' => 'sometimes|required|string|max:255',
            ]);

            $device->update($validated);

            return response()->json([
                'success' => true,
                'data' => $device,
                'message' => 'IP device berhasil diupdate',
            ]);
        } catch (\Illuminate\Database\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'IP device tidak ditemukan',
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
            $device = IpDevice::findOrFail($id);
            $device->delete();

            return response()->json([
                'success' => true,
                'message' => 'IP device berhasil dihapus',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'IP device tidak ditemukan',
            ], 404);
        }
    }
}
