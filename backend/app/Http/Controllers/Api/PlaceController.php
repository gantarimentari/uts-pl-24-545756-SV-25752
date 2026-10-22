<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;


class PlaceController extends Controller
{
    // Menampilkan semua tempat milik pengguna yang login
    public function index(Request $request)
    {
        $places = $request->user()
            ->places()
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar tempat berhasil diambil.',
            'data' => $places,
        ]);
    }

    // Menambahkan tempat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'visit_date' => ['nullable', 'date'],
        ]);

        $place = $request->user()->places()->create($validated);

        return response()->json([
            'message' => 'Tempat berhasil ditambahkan.',
            'data' => $place,
        ], 201);
    }

    // Menampilkan detail satu tempat
    public function show(Request $request, string $id)
    {
        $place = $request->user()->places()->findOrFail($id);

        return response()->json([
            'message' => 'Detail tempat berhasil diambil.',
            'data' => $place,
        ]);
    }

    // Mengubah tempat
    public function update(Request $request, string $id)
    {
        $place = $request->user()->places()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'description' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'visit_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $place->update($validated);

        return response()->json([
            'message' => 'Tempat berhasil diperbarui.',
            'data' => $place->fresh(),
        ]);
    }

    // Menghapus tempat
    public function destroy(Request $request, string $id)
    {
        $place = $request->user()->places()->findOrFail($id);

        $place->delete();

        return response()->json([
            'message' => 'Tempat berhasil dihapus.',
        ]);
    }
}