<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $folder = uniqid('image', true);
            $image->storeAs('tmpimages/user' . auth()->user()->id . '/' . $folder, $filename, ['disk' => 'public']);

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
            File::deleteDirectory(public_path('tmpimages/user' . auth()->user()->id . '/' . $temporaryImage->folder));
            $temporaryImage->delete();
        }

        return '';
    }

    public function destroy(Image $image)
    {
        $image = Image::find($image->id);
        File::delete(public_path('images/tasks/user' . auth()->user()->id . '/' . $image->path));
        $image->delete();

        return '';
    }
}
