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


    // Override standard login to support email or NIP
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $identifier = $request->input($this->username());
        $password = $request->password;

        $user = User::findByEmailOrNip($identifier)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // View untuk pengawas login
    public function showPengawasLoginForm()
    {
        // Jika sudah login, arahkan ke dashboard masing-masing
        if (Auth::check()) {
            return redirect($this->redirectTo());
        }
        return view('dashboard_pengawas.login');
    }

    // View untuk stakeholder login
    public function showStakeholderLoginForm()
    {
        // Jika sudah login, arahkan ke dashboard masing-masing
        if (Auth::check()) {
            return redirect($this->redirectTo());
        }
        return view('stakeholder.login');
    }

    // Metode untuk login stakeholder
    public function stakeholderLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required', // Bisa email atau NIP
            'password' => 'required',
        ]);

        // Cari pengguna berdasarkan email atau NIP di tabel users
        $user = User::findByEmailOrNip($request->identifier)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role == 'Stakeholder') {
                Auth::login($user);
                return redirect()->route('admin.index');
            } else {
                Session::flash('error', 'Anda tidak punya akses untuk halaman ini.');
                return redirect()->route('stakeholder.login');
            }
        } else {
            return redirect()->route('stakeholder.login')->withErrors([
                'identifier' => 'Email/NIP atau password salah.',
            ]);
        }
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

    if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role == 'Pengawas') {
        Auth::login($user);
            return redirect()->route('pengawas.index');
        } else {
            Session::flash('error', 'Anda tidak punya akses untuk halaman ini.');
            return redirect()->route('pengawas.login');
        }
    } else {
        return redirect()->route('pengawas.login')->withErrors([
                'identifier' => 'NIP/Email atau password salah.',
        ]);
    }
}

    public function logout(Request $request)
    {
        $role = Auth::user() ? Auth::user()->role : null;

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($role == 'Stakeholder') {
            return redirect()->route('stakeholder.login');
        } elseif ($role == 'Pengawas') {
            return redirect()->route('pengawas.login');
        }

        return redirect('/');
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