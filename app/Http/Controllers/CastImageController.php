<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cast_image;
use App\Services\ImageCompressor;

class CastImageController extends Controller
{
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $filename);
            ImageCompressor::compressIfNeeded(public_path('uploads') . '/' . $filename);

            $Castimg = new Cast_image();
            $Castimg->cast_id = $id;
            $Castimg->filename = 'uploads/' . $filename;
            $Castimg->save();

            return response()->json(['success' => true, 'image' => $Castimg]);
        }

        return response()->json(['success' => false], 400);
    }


    public function getImages($id)
    {
        $images = Cast_image::where('cast_id', $id)->get();
        return view('admin.casts.partials.image-list', compact('images'));
    }


    public function toggleFlag($id, $imageId)
    {
        $image = Cast_image::findOrFail($imageId);
        $image->flag = !$image->flag;
        $image->save();

        return response()->json(['success' => true]);
    }


    public function deleteImage($id, $imageId)
    {
        $image = Cast_image::findOrFail($imageId);
        if (file_exists(public_path($image->filename))) {
            unlink(public_path($image->filename));
        }
        $image->delete();

        return response()->json(['success' => true]);
    }


    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $index => $id) {
            CastImage::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }



}
