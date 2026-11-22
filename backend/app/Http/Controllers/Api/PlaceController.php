<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public function store(Request $request)
    {
        // Validasi input sebelum menyimpan data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'visit_date' => ['nullable', 'date'],
        ]);

        // WARNING: validasi gagal
        if ($validator->fails()) {
            Log::warning('place_validation_failed', [
                'actor' => 'authenticated_user',
                'action' => 'create_place',
                'reason' => 'input_validation_failed',
                'component' => 'place_controller',
                'result' => 'rejected',
            ]);

            // Mempertahankan respons validasi standar Laravel
            throw new ValidationException(
                $validator
            );
        }

        $validated = $validator->validated();

        try {
            // Menyimpan data tempat ke database
            $place = $request->user()
                ->places()
                ->create($validated);

            // INFO: penyimpanan berhasil
            Log::info('place_created', [
                'actor' => 'authenticated_user',
                'action' => 'create_place',
                'reason' => 'database_insert_succeeded',
                'component' => 'place_controller',
                'result' => 'success',
            ]);

            return response()->json([
                'message' => 'Tempat berhasil ditambahkan.',
                'data' => $place,
            ], 201);

        } catch (QueryException $e) {
            // ERROR: operasi database gagal
            Log::error('place_database_error', [
                'actor' => 'authenticated_user',
                'action' => 'create_place',
                'reason' => 'database_query_failed',
                'component' => 'database',
                'result' => 'failed',
            ]);

            return response()->json([
                'message' => 'Layanan database sedang bermasalah.',
            ], 503);
        }
    }

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
