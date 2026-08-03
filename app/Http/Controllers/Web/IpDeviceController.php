<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\IpDevice;
use Illuminate\Http\Request;

class IpDeviceController extends Controller
{
    public function index(Request $request)
    {
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

        $devices = $query->orderBy('name')->get();
        $locations = IpDevice::distinct()->pluck('location')->sort()->values();

        return view('ip-devices.index', compact('devices', 'locations'));
    }

    public function create()
    {
        return view('ip-devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'specifications' => 'nullable|string|max:500',
            'ip_address' => 'required|string|max:45|unique:ip_devices,ip_address',
            'location' => 'required|string|max:255',
        ]);

        IpDevice::create($validated);

        return redirect()->route('ip-devices.index')->with('success', 'IP device berhasil ditambahkan.');
    }

    public function edit(IpDevice $ipDevice)
    {
        return view('ip-devices.edit', ['device' => $ipDevice]);
    }

    public function update(Request $request, IpDevice $ipDevice)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'specifications' => 'nullable|string|max:500',
            'ip_address' => 'required|string|max:45|unique:ip_devices,ip_address,' . $ipDevice->id,
            'location' => 'required|string|max:255',
        ]);

        $ipDevice->update($validated);

        return redirect()->route('ip-devices.index')->with('success', 'IP device berhasil diupdate.');
    }

    public function destroy(IpDevice $ipDevice)
    {
        $ipDevice->delete();
        return redirect()->route('ip-devices.index')->with('success', 'IP device berhasil dihapus.');
    }
}
