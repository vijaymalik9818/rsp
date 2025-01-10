<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('show-agents');
        }

        return view('login');
    }

    public function showleads()
    {
        return view('apps-crm-leads');
    }

    public function dashboard()
    {
        return view('apps-crm-leads');
    }

    public function signin(Request $request)
    {
     
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
        $token = Str::random(64);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            DB::table('users')
            ->where('email', $user->email)
            ->update([
                'remember_token' => $token,
                'created_at' => now()
            ]);
            if ($user->change_pass_status === null && $user->role === null) {
                return redirect("/reset-password/" . $token);
            }
    
           else if ($user->role == 1) {
                return redirect('/agents');
            }
          else{
            return redirect("/agent-details/" . base64_encode($user->id));
          }
        } else {
            session()->flash('error', 'Please enter correct Email or password');
            return redirect()->back();
        }
    }
    

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function faileduser(){
        return view('faileduser');
    }
}
