<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CkeditorController extends Controller
{
    //
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $originalName = $request->file('upload')->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $request->file('upload')->getClientOriginalExtension();
                $fileName = $fileName . '_' . time() . '.' . $extension;

                $request->file('upload')->move(public_path('uploads/gallery'), $fileName);

                $url = asset('uploads/gallery/' . $fileName);

                return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     dd($request->all());
    // }

    public function deleteImages(Request $request)
    {
        try {
            $imagesToDelete = $request->input('imagesToDelete');
            $galleryPath = public_path('uploads/gallery');

            foreach ($imagesToDelete as $image) {
                $imagePath = $galleryPath . '/' . $image;

                // Kiểm tra xem tập tin có tồn tại trước khi thử xóa
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            return response()->json(['message' => 'Xóa ảnh thành công']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}