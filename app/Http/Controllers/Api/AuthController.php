<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'team_id' => 'nullable|exists:teams,id',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = 'technician';

            $user = User::create($validated);
            $token = $user->createToken('mobile-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => ['user' => $user, 'token' => $token],
                'message' => 'Registrasi berhasil',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Email atau password salah.'],
                ]);
            }

            if (!$user->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Akun tidak aktif.'],
                ]);
            }

            $token = $user->createToken('mobile-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => ['user' => $user, 'token' => $token],
                'message' => 'Login berhasil',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout',
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user()->load('team');

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Data user berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user',
            ], 500);
        }
    }
}
