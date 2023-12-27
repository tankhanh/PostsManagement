<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $originalName = $request->file('upload')->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $request->file('upload')->getClientOriginalExtension();
                $fileName = $fileName . '_' . time() . '.' . $extension;

                // Lưu tập tin vào thư mục storage
                $request->file('upload')->storeAs('uploads/gallery', $fileName, 'public');

                $url = Storage::url('uploads/gallery/' . $fileName);

                return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function deleteImages(Request $request)
    {
        try {
            $imagesToDelete = $request->input('imagesToDelete');
            
            foreach ($imagesToDelete as $image) {
                // Xóa tệp tin khỏi thư mục storage
                Storage::disk('public')->delete('uploads/gallery/' . $image);
            }

            return response()->json(['message' => 'Xóa ảnh thành công']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}