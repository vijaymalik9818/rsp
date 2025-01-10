<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendResetPasswordEmail;
use Mailgun\Mailgun;
use log;

use function Laravel\Prompts\password;

class ForgotPasswordController extends Controller
{
    // use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {

        return view('forget');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email)->first();
        if (!$user) {
            session()->flash('error', 'Sorry! Email address does not exist');
            return redirect()->back();
        }
        // $user= Auth::user();
   
        // if ($user->role === 1) {
            $latestAdmin = User::where('role', 1)->latest()->first();
 
          $adminname = $latestAdmin->name;
          $adminemail = $latestAdmin->email;
//  dd($adminname);
         
        // }
        $token = Str::random(64);
        $expiration = now()->addMinutes(5);
    
        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'remember_token' => $token,
                'token_expires_at' => $expiration,
                'change_pass_status' => 0,
                'created_at' => now()
            ]);
    
        try {
                $mg = Mailgun::create(env('MAILGUN_API_KEY')); 
            
                $params = [
                    'from'    => env('MAIL_FROM_ADDRESS'),
                    'to'      => $request->email,
                    'subject' => "Welcome to Real Estate Professionals Inc.!",
                    'html'    => view('forget-password', [
                        'token'      => $token,
                        'name'       => $user->name,
                        'adminname'  => $adminname,
                        'adminemail' => $adminemail
                    ])->render()
                ];
            
                $result = $mg->messages()->send(env('MAILGUN_DOMAIN'), $params);
            
            } catch (\Exception $e) {
                Log::error("Failed to send email: " . $e->getMessage());
            }
    
    
        session()->flash('success', 'Email sent successfully');
        return redirect()->back();
    }
    
}

