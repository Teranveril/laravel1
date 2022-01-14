<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UsersPasswordHistory;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if(!isset(Auth::user()->last_password_change)){
                Auth::logout();
                return redirect()->route('password.reset-password')->with('error', 'Pierwsze logowanie - ustal hasło');
            } else if(strtotime(Auth::user()->last_password_change) < strtotime('-30 days')) {
                Auth::logout();
                return redirect()->route('password.reset-password')->with('error', 'Upłynięcie 30 dni - zmień hasło');
            } else {
                return redirect()->intended('home');
            }
        }

        return redirect()->route('login')
            ->with('error', 'Email-Address And Password Are Wrong.');
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'new_password' =>['required','string', function($field, $data, $fail){
                if(strlen($data) < 8 || !preg_match("/^(?=(.*[A-Z]){2})(?=(.*[a-z]){2})(?=(.*[@_!#$%^&*()<>?|}{~:]){2})(?=(.*[0-9]){2})\w+$/",$data)) return redirect()->route('password.reset-password')->with('error', 'Hasło powinno składać się z minimum 8 znaków przy założeniu, że zawiera minimum 2 małe litery, 2 duże litery, 2 cyfry, 2 znaki specjalne.');
            }]
        ]);


        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'new_password' =>['required','string', function($field, $data, $fail){
                if(strlen($data) < 8 || !preg_match("/^(?=(.*[A-Z]){2})(?=(.*[a-z]){2})(?=(.*[0-9]){2})\w+$/",$data)) $fail('new_password');
//                if(strlen($data) < 8 || !preg_match("/^(?=(.*[A-Z]){2})(?=(.*[a-z]){2})(?=(.*[@_!#$%^&*()<>?|}{~:]){2})(?=(.*[0-9]){2})\w+$/",$data)) $fail('new_password');
            }]
        ]);

        if ($validator->fails()) {
            return redirect()->route('password.reset-password')->with('error', 'Hasło powinno składać się z minimum 8 znaków przy założeniu, że zawiera minimum 2 małe litery, 2 duże litery, 2 cyfry, 2 znaki specjalne.');
        }
        $user = User::where('email',$request->email)->with('oldPasswords')->first();
        if($user && !$user->checkPassword($request->new_password, $user->oldPasswords)){
            return redirect()->route('password.reset-password')->with('error', 'Hasło zostało już kiedyś użyte.');
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            UsersPasswordHistory::create(['user_id'=>Auth::user()->id,'password', $request->password]);
            User::find(Auth::user()->id)->update(['password'=> Hash::make($request->new_password)]);
            Auth::user()->update(['last_password_change'=>now()]);
            Mail::to($user->email)->send(new PasswordChange());
            return redirect()->intended('home');
        }

        return redirect()->route('password.reset-password')->with('error', 'Obecne hasło jest nieprawidłowe');
    }


    public function index()
    {
        return view('auth.login');
    }

}
