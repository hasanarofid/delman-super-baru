<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Facades\Session;
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


    // View untuk pengawas login
    public function showPengawasLoginForm()
    {
        return view('dashboard_pengawas.login');
    }



    // Metode untuk login pengawas
    public function superPengawasLogin(Request $request)
{
    
    $request->validate([
        'identifier' => 'required', // Bisa email atau NIP
        'password' => 'required',
    ]);

    // Cari pengguna berdasarkan email atau NIP
    $user = User::findByEmailOrNip($request->identifier)->first();
    // dd($user);
    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);
        
        if ($user->role == 'Pengawas') {
            return redirect()->route('pengawas.index');
        } else {
            Auth::logout();
            Session::flash('error', 'Anda tidak punya akses untuk halaman ini.');
            return redirect()->route('pengawas.login');
        }
    } else {
        return redirect()->route('pengawas.login')->withErrors([
            'identifier' => 'NIP atau password salah.',
        ]);
    }
}


    public function logoutpengawas(Request $request)
    {
        // Add your custom logout logic here, if needed

        // Logout the user
        Auth::logout();

        // Clear the session data
        $request->session()->invalidate();
        // dd(2);
        // Redirect to the login page or any other page you prefer
        return redirect('/pengawas/login');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // protected $redirectTo = RouteServiceProvider::HOME;
    protected function redirectTo()
    {
        // if (Auth::user()->role == 'Admin') {
            return '/dashboard';
        // } 
        // else {
        //     return '/';
        // }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}