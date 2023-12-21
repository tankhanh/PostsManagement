<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login (LoginRequest $request){
        $credentitals = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        $remember = $request->has('remember');
        if(Auth::attempt($credentitals, $remember))
        {
            return redirect()->route('posts.index');
        } else
        {
            return redirect()->route('login')->withErrors(['password' => 'Wrong password or email. Please check it again!']);
        }
    }
}