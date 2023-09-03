<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Task;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $folder = uniqid('image', true);
            $image->storeAs('tasks/images/' . $folder, $filename, ['disk' => 'public']);

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
            File::deleteDirectory(public_path('tasks/images/' . $temporaryImage->folder));
            $temporaryImage->delete();
        }

        return '';
    }

    public function destroy(Image $image)
    {
        $image = Image::find($image->id);
        File::delete(public_path('tasks/images/' . $image->path));
        File::deleteDirectory(public_path('tasks/images'));
        $image->delete();

        return '';
    }
}
