<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Profile;
use App\Events\CreateProfileFromAccount;
class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(RegisterRequest $request)
    {
        // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu hay chưa
        $existingAccount = Account::where('email', $request->email)->first();

        if ($existingAccount) {
            // Xử lý khi email đã tồn tại
            return redirect()->back()->with('error', 'This email is already in use.');
        }

        $firstname = $request->firstname;
        $lastname = $request->lastname;

        if (empty($firstname) || empty($lastname)) {
            // Xử lý khi firstname hoặc lastname trống
            return redirect()->back()->with('error', 'Firstname and lastname are required.');
        }

        // Tạo một bản ghi mới trong bảng profiles
        $profile = new Profile();
        $profile->firstname = $firstname;
        $profile->lastname = $lastname;
        $profile->save();

        // Tạo một bản ghi mới trong bảng accounts
        $account = new Account();
        $account->email = $request->email;
        $account->password = bcrypt($request->password);
        $account->role = 2;

        // Gán khóa ngoại profile_id trong bảng accounts với ID của bản ghi profile mới
        $account->profile_id = $profile->id;
        $account->save();
        $account->firstname = $firstname; // Đặt thuộc tính firstname
        $account->lastname = $lastname;   // Đặt thuộc tính lastname
        // Gửi sự kiện để tạo bản ghi trong bảng profiles
        event(new CreateProfileFromAccount($account));

        return redirect()->route('login');
    }

}