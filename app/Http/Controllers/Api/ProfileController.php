<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->user()->load('team');

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profil berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil profil',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $file = $request->file('avatar');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars', $filename, 'public');
                $validated['avatar'] = $path;
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user->fresh()->load('team'),
                'message' => 'Profil berhasil diupdate',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate profil',
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai',
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password',
            ], 500);
        }
    }
}
