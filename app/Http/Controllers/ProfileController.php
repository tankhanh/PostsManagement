<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        $edit_myself = null;
        if (Auth::user()->id) {
            $edit_myself = true;
        } else {
            $edit_myself = false;
        }
        return response()->view('profile.edit', compact('edit_myself'));
    }
    public function update(UpdateRequest $request)
    {
        $account = Account::findOrFail(Auth::user()->id);
        $profile = Auth::user()->profile;

        if ($profile === null) {
            // Xử lý trường hợp $profile là null
            return redirect()->route('posts.index')->with('error', 'Profile not found');
        }

        // Update password if provided
        if (!empty($request->password)) {
            $request->validate([
                'password' => 'required|confirmed|min:8'
            ], [
                'password.required' => 'Please enter a password',
                'password.confirmed' => 'Confirmation password doesn\'t match',
                'password.min' => 'The password must be at least 8 characters.',
            ]);

            $account->password = bcrypt($request->password);
        }
        $file = $request->profile_pic;
        // Update profile picture if provided
        if (!empty($file)) {
            // Xóa ảnh hồ sơ cũ nếu có
            if (!empty($profile->profile_pic)) {
                $old_image_path = 'uploads/avatar/' . $profile->profile_pic;
                if (Storage::disk('public')->exists($old_image_path)) {
                    Storage::disk('public')->delete($old_image_path);
                }
            }
            $request->validate([
                'profile_pic' => 'required|mimes:jpg,jpeg,png,svg'
            ], [
                'profile_pic.required' => 'Please select an image',
                'profile_pic.mimes' => 'Images must be in jpg, jpeg, png, or svg format',
            ]);
    
            $fileName = time() . '-' . $file->getClientOriginalName();
            $profile->profile_pic = $fileName;
            Storage::disk('public')->put('uploads/avatar/' . $fileName, file_get_contents($file));
        }

        // Update other profile fields
        $profile->firstname = $request->input('firstname');
        $profile->lastname = $request->input('lastname');
        $profile->gender = $request->input('gender');
        $profile->birthday = $request->input('birthday');
        
        // Save changes
        $account->save();
        $profile->save();

        return redirect()->route('posts.index')->with('success', 'Profile updated successfully');
    }

}