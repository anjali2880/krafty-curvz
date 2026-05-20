<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CustomizationController extends Controller
{
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('image')->store('customizations/uploads', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }

    public function saveDesign(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'design_data' => 'required|string',
            'design_image' => 'required|string',
        ]);

        $imageData = $request->input('design_image');
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageBinary = base64_decode($imageData);

        $filename = 'custom_' . $request->product_id . '_' . time() . '.png';
        $path = 'customizations/' . $filename;

        Storage::disk('public')->put($path, $imageBinary);

        $savedCustomizations = $request->session()->get('saved_customizations', []);
        $savedCustomizations[(int) $request->product_id] = [
            'design_data' => json_decode((string) $request->design_data, true),
            'design_image' => $path,
        ];
        $request->session()->put('saved_customizations', $savedCustomizations);

        return response()->json([
            'success' => true,
            'image_path' => $path,
            'image_url' => asset('storage/' . $path),
            'design_data' => $request->design_data,
        ]);
    }
}
