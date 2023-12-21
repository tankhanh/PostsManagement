<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Account;


class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(RegisterRequest $request)
    {
        $existingAccount = Account::where('email', $request->email)->first();

        if ($existingAccount) {
            // Xử lý khi email đã tồn tại
            return redirect()->back()->with('error', 'This email is already in use.');
        }
        $account = new Account();
        $account->email = $request->email;
        $account->password = bcrypt($request->password);

        $account->save();

        return redirect()->route('login');
    }
}