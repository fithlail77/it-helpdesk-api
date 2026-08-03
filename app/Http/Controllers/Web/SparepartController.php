<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = Sparepart::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $spareparts = $query->orderBy('name')->get();

        return view('spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        return view('spareparts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Sparepart::create($validated);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil ditambahkan.');
    }

    public function edit(Sparepart $sparepart)
    {
        return view('spareparts.edit', compact('sparepart'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $sparepart->update($validated);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diupdate.');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus.');
    }
}
