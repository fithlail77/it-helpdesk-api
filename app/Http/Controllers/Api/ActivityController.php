<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Activity::with(['assignee', 'team', 'creator']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('reporter_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            $perPage = $request->get('per_page', 15);
            $activities = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Daftar aktivitas berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data aktivitas',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|string|in:hardware,software,network,other',
                'sub_category' => 'nullable|string|max:100',
                'device_type' => 'nullable|string|max:100',
                'barcode_number' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:100',
                'priority' => 'required|string|in:low,medium,high,urgent',
                'reporter_name' => 'required|string|max:255',
                'reporter_phone' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'photo_path' => 'nullable|string|max:255',
                'assigned_to' => 'nullable|exists:users,id',
                'team_id' => 'nullable|exists:teams,id',
            ]);

            $validated['created_by'] = $request->user()->id;
            $validated['status'] = 'pending';

            $activity = Activity::create($validated);

            ActivityLog::create([
                'activity_id' => $activity->id,
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'note' => 'Aktivitas dibuat',
            ]);

            return response()->json([
                'success' => true,
                'data' => $activity->load(['assignee', 'team', 'creator']),
                'message' => 'Aktivitas berhasil dibuat',
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
                'message' => 'Terjadi kesalahan saat membuat aktivitas',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $activity = Activity::with(['assignee', 'team', 'creator', 'logs.user', 'logs.sparepart'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $activity,
                'message' => 'Detail aktivitas berhasil diambil',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data aktivitas',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $activity = Activity::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'category' => 'sometimes|string|in:hardware,software,network,other',
                'sub_category' => 'nullable|string|max:100',
                'device_type' => 'nullable|string|max:100',
                'barcode_number' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:100',
                'priority' => 'sometimes|string|in:low,medium,high,urgent',
                'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
                'reporter_name' => 'sometimes|string|max:255',
                'reporter_phone' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'assigned_to' => 'nullable|exists:users,id',
                'team_id' => 'nullable|exists:teams,id',
                'repair_data' => 'nullable|array',
                'repair_data.description' => 'required_with:repair_data|string',
                'repair_data.stock_part_used' => 'nullable|boolean',
                'repair_data.stock_part_name' => 'nullable|string',
                'repair_data.stock_part_quantity' => 'nullable|integer|min:1',
                'spareparts' => 'nullable|array',
                'spareparts.*.id' => 'nullable|exists:spareparts,id',
                'spareparts.*.quantity' => 'nullable|integer|min:1',
            ]);

            $statusChanged = isset($validated['status']) && $validated['status'] !== $activity->status;

            if ($statusChanged && $validated['status'] === 'completed') {
                $validated['completed_at'] = now();
            }

            $repairData = $validated['repair_data'] ?? null;
            $rawSpareparts = $validated['spareparts'] ?? [];
            $spareparts = array_filter($rawSpareparts, fn($item) => !empty($item['id']));

            unset($validated['repair_data'], $validated['spareparts']);

            $activity->update($validated);

            if ($statusChanged) {
                $logNote = "Status diubah dari '{$activity->getOriginal('status')}' ke '{$validated['status']}'";
                if ($repairData && isset($repairData['description'])) {
                    $logNote = $repairData['description'];
                }

                $log = ActivityLog::create([
                    'activity_id' => $activity->id,
                    'user_id' => $request->user()->id,
                    'status' => $validated['status'],
                    'note' => $logNote,
                    'repair_data' => $repairData,
                ]);

                $first = true;
                foreach ($spareparts as $item) {
                    if (empty($item['id'])) continue;

                    $sparepart = Sparepart::find($item['id']);
                    $qty = $item['quantity'] ?? 1;

                    if ($sparepart && $sparepart->stock >= $qty) {
                        $sparepart->decrement('stock', $qty);

                        if ($first) {
                            $log->update([
                                'sparepart_id' => $sparepart->id,
                                'sparepart_quantity' => $qty,
                                'sparepart_price' => $sparepart->price,
                            ]);
                            $first = false;
                        } else {
                            ActivityLog::create([
                                'activity_id' => $activity->id,
                                'user_id' => $request->user()->id,
                                'status' => $validated['status'],
                                'note' => 'Sparepart: ' . $sparepart->name,
                                'sparepart_id' => $sparepart->id,
                                'sparepart_quantity' => $qty,
                                'sparepart_price' => $sparepart->price,
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $activity->load(['assignee', 'team', 'creator']),
                'message' => 'Aktivitas berhasil diupdate',
            ]);
        } catch (\Illuminate\Database\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate aktivitas',
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $activity = Activity::findOrFail($id);

            if ($activity->status === 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktivitas yang sedang berlangsung tidak dapat dihapus',
                ], 422);
            }

            ActivityLog::create([
                'activity_id' => $activity->id,
                'user_id' => $request->user()->id,
                'status' => $activity->status,
                'note' => 'Aktivitas dihapus',
            ]);

            $activity->delete();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus',
            ]);
        } catch (\Illuminate\Database\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus aktivitas',
            ], 500);
        }
    }

    public function uploadPhoto(Request $request, Activity $activity)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $file = $request->file('photo');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('activities', $filename, 'public');

            $activity->update([
                'photo_path' => $path,
            ]);

            ActivityLog::create([
                'activity_id' => $activity->id,
                'user_id' => $request->user()->id,
                'status' => $activity->status,
                'note' => 'Foto aktivitas diupload',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'photo_path' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
                ],
                'message' => 'Foto berhasil diupload',
            ]);
        } catch (\Illuminate\Database\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload foto',
            ], 500);
        }
    }
}
