<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\TemporaryImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'user' => [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'username' => auth()->user()->username,
                'avatar' => auth()->user()->avatar,
                'default_avatar' => auth()->user()->getAvatar(),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateinformation(Request $request)
    {
        $attributes = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
        ]);

        if (request('image')) {
            $temporaryImage = TemporaryImage::where('folder', $request->image)->firstOrFail();
            $attributes['avatar'] = $temporaryImage->folder . '/' .$temporaryImage->file;

            File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images');
            File::cleanDirectory(public_path() . '/tmpimages/user' . auth()->user()->id);
            $temporaryImage->delete();
        }

        auth()->user()->update($attributes);

        return to_route('settings.index')->with('message', 'Personal information updated successfully!');
    }

    public function updatePassword()
    {
        $attributes = request()->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        if (!password_verify($attributes['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        auth()->user()->update([
            'password' => bcrypt($attributes['new_password']),
        ]);

        return to_route('settings.index')->with('message', 'Password updated successfully!');
    }

    public function deleteAvatar()
    {
        File::delete(public_path('images/user' . auth()->user()->id . '/' . auth()->user()->avatar));
        auth()->user()->update([
            'avatar' => null,
        ]);

        return to_route('settings.index')->with('message', 'Avatar deleted successfully!');
    }

    public function deleteAccount(Request $request)
    {
        File::cleanDirectory(public_path() . '/images/user' . auth()->user()->id);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $user = User::find(request('user'));
        $user->delete();

        return redirect('/')->with('message', 'Account deleted successfully!');
    }
}
