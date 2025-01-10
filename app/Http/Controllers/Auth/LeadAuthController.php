<?php

namespace App\Http\Controllers\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use App\Models\Lead;
use App\Models\LoginInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Support\Facades\Redirect;
use Mailgun\Mailgun;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

class LeadAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
           
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        
        $lead = Lead::where('email', $request->email)->first();
    
        if (!$lead || !Hash::check($request->password, $lead->password)) {
            return response()->json(['errors' => ['email' => ['The provided credentials are incorrect.']]], 422);
        }
    
        $denverTimeZone = 'America/Denver';
        $now = Carbon::now($denverTimeZone);
        $token = $lead->createToken('Laravel Password Grant Client')->accessToken;
        $loginInformation = new LoginInformation();
        $loginInformation->user_id = $lead->id;
        $loginInformation->login_timestamp = $now;
        $loginInformation->save();
    
        session()->put('username', $lead->name);
        session()->put('user_id', $lead->id);
        session()->put('user_image', $lead->profile_picture);
    
        return response()->json([
            'message' => 'Lead authenticated successfully',
            'name' => $lead->name,
            'id' => $lead->id,
            'profile_picture' => $lead->profile_picture,
            'token' => $token 
        ], 200);
    }
   
    
    public function getdetails(Request $request)
    {
        $user = $request->authenticated_user;

        return response()->json(['user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $lead = $request->authenticated_user;
        if ($lead) {
            $accessToken = $lead->token();
            $accessToken->revoke();
            return response()->json(['message'=>'logout successful']);
        }
        return response()->json(['error'=>'logout failed']);
    }

    public function leadInfo(Request $request)
    {
       $header =  $request->header('authorization');
    
        $user = Auth::lead(); 
    
        $lead = $user->lead;

        return response()->json(['lead' => $lead], 200);
    }
  

    public function updatePassword(Request $request, $id)
    {
        try {
            $lead = Lead::findOrFail($id);

            $lead->update([
                'password' => bcrypt($request->input('newPassword')),
            ]);

            return response()->json(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating password: ' . $e->getMessage()], 500);
        }
    }
    public function signup(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
                'role' => 'required|string', 
             
            ]);
          $logintype = 'sign-up';
            $lead = new Lead();
            $lead->name = $validatedData['fullname'];
            $lead->email = $validatedData['email'];
            $lead->phone = $validatedData['phone'];
            $lead->login_type = $logintype;
            // $lead->profile_picture = $validatedData['picture'];
            $lead->password = bcrypt($validatedData['password']);
            $lead->role = $validatedData['role']; 
            $lead->save();
            $latestAdmin = User::where('role', 1)->latest()->first();
    
            $adminname = $latestAdmin->name;
            $adminemail = $latestAdmin->email;
            // Send welcome email using SendGrid
            $mg = Mailgun::create(env('MAILGUN_API_KEY'));

            $params = [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to'      => $validatedData['email'],
                'subject' => 'Welcome to Real Estate Professionals Inc.!',
                'html'    => view('emails.welcomemail', [
                    'name'       => $validatedData['fullname'],
                    'adminname'  => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ];
    
            $result = $mg->messages()->send(env('MAILGUN_DOMAIN'), $params);
    
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            if (isset($errors['email']) && in_array('The email has already been taken.', $errors['email'])) {
                return response()->json(['message' => 'Email already exists.'], 200);
            }
            return response()->json(['message' => 'Validation error: ' . json_encode($errors)], 422);
        } catch (\Exception $e) {
            Log::error("Error signing up: " . $e->getMessage());
           
            return response()->json(['message' => 'Error signing up: ' . $e->getMessage()], 500);
        }
    }
    public function socialsignup(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'required', 'email',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
                'login_type' => 'nullable',
                'picture' => 'nullable'
            ]);
    
            $logintype = $request->login_type;
            $email = $request->email;
            $password = $request->password;
    
            if ($logintype == 'google' || $logintype == 'facebook') {
                $existingUser = Lead::where('email', $email)->first();
                if ($existingUser) {
                    return $this->sociallogin($email, $password);
                }
             else {
                $lead = new Lead();
                $lead->name = $validatedData['fullname'];
                $lead->email = $validatedData['email'];
                $lead->phone = $validatedData['phone'];
                $lead->login_type = $validatedData['login_type'];
                $lead->profile_picture = $validatedData['picture'];
                $lead->password = bcrypt($validatedData['password']);
                $lead->save();
                $token = $lead->createToken('Laravel Personal Access Client')->accessToken;
                return $this->sociallogin($email, $password);
                
            }
        }
        else{
            return response()->json(['message' => 'Invalid user.'], 200);
        } 
    }catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            if (isset($errors['email']) && in_array('The email has already been taken.', $errors['email'])) {
                return response()->json(['message' => 'Email already exists.'], 200);
            }
            return response()->json(['message' => 'Validation error: ' . json_encode($errors)], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error signing up: ' . $e->getMessage()], 500);
        }
    }
    
    public function sociallogin($email, $password)
    {
        $lead = Lead::where('email', $email)->first();
      
        $denverTimeZone = 'America/Denver';
        $now = Carbon::now($denverTimeZone);
        $token = $lead->createToken('Laravel Password Grant Client')->accessToken;
        $loginInformation = new LoginInformation();
        $loginInformation->user_id = $lead->id;
        $loginInformation->login_timestamp = $now;
        $loginInformation->save();
    
        session()->put('username', $lead->name);
        session()->put('user_id', $lead->id);
        session()->put('user_image', $lead->profile_picture);
    
        return response()->json([
            'message' => 'Lead authenticated successfully',
            'name' => $lead->name,
            'id' => $lead->id,
            'profile_picture' => $lead->profile_picture,
            'token' => $token
        ], 200);
    }
    
    function checkemail(Request $request)
    {
        $email = $request->email;

        $user = Lead::where('email', $email)->first();

        if ($user) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
    function showresetfrontform($token)
    {

        if ($this->isTokenExpired($token)) {
            return response()->json(['message' => 'Reset link expired please request a new one'], 200);
        }
        return view('reset-passwords', ['token' => $token]);
    }

    public function resets(Request $request)
    {
        $token = $request->token;
        // dd($token);
        $frontend_url = env('FRONTEND_URL');
        if ($this->isTokenExpired($token)) {
            return response()->json(['message' => 'Reset link expired please request a new one'], 200);
        }

        $request->validate([
            'password' => 'required',
        ]);

        $user = DB::table('leads')->where('token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token password reset failed'], 200);
        }

        if ($user->change_pass_status == 1) {
            return response()->json(['message' => 'Reset link has already been used'], 200);
        }

        DB::table('leads')
            ->where('token', $token)
            ->update([
                'password' => Hash::make($request->password),
                'change_pass_status' => 1,
                'token_expires_at' => now(),
            ]);

        Auth::logout();

        return Redirect::to($frontend_url);
    }

    public function isTokenExpired($token)
    {
        $user = DB::table('leads')->where('token', $token)->first();

        if (!$user) {
            return true;
        }
        return now()->gt($user->token_expires_at);
    }
    public function getlogininfo($id)
    {
        try {
            $loginInfo = LoginInformation::where('user_id', $id)
                ->orderBy('login_timestamp', 'desc')
                ->limit(50) 
                ->paginate(10); 
    
            return response()->json(['login_info' => $loginInfo], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve login information'], 500);
        }
    }
 

}
