<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    function showresetform($token)
    {
    
        if ($this->isTokenExpired($token)) {
            return redirect()->route('forgot')->with('error', 'Reset link has expired. Please request a new one.');
        }
        return view('reset-password', ['token' => $token]);
    }
   
    public function reset(Request $request)
    {
        $token = $request->token;
        if ($this->isTokenExpired($token)) {
            return redirect()->route('login')->with('error', 'Reset link has expired. Please request a new one.');
        }

        $request->validate([
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('remember_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid token. Password reset failed.');
        }

        if ($user->change_pass_status == 1) {
            return redirect()->route('forgot')->with('error', 'Reset link has already been used. Please request a new one.');
        }

        DB::table('users')
            ->where('remember_token', $token)
            ->update([
                'password' => Hash::make($request->password),
                'change_pass_status' => 1,
                'token_expires_at' => now(),
            ]);

        Auth::logout();
        return redirect()->route('login')->with('success', 'Password reset successful. Please login with your new password.');
    }

    public function isTokenExpired($token)
    {
        $user = DB::table('users')->where('remember_token', $token)->first();

        if (!$user) {
            return true;
        }
        return now()->gt($user->token_expires_at);
    }
}
