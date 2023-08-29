<?php

namespace App\Http\Controllers;

use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $folder = uniqid('image', true);
            $image->storeAs('tasks/images/tmp/' . $folder, $filename);

            TemporaryImage::create([
                'folder' => $folder,
                'file' => $filename,
            ]);

            return $folder;
        }

        return '';
    }

    public function revert($folder)
    {
        $temporaryImage = TemporaryImage::where('folder', $folder)->firstOrFail();
        if ($temporaryImage) {
            Storage::deleteDirectory('tasks/images/tmp/' . $temporaryImage->folder);
            $temporaryImage->delete();
        }

        return '';
    }
}
