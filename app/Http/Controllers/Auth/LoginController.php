<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '#/campaign-profile-list';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
     /**
     * Check either username or email.
     * @return string
     */
    public function username()
    {
        $identity  = request()->get('identity');
        $fieldName = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldName => $identity]);

        return $fieldName;
    }

    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    { 
        $this->validate(
            $request,
            [
                'identity' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'identity.required' => 'Username or email is required',
                'password.required' => 'Password is required',
            ]
        );
    }

    /**
     * @param Request $request
     * @throws ValidationException
     * this is working for laravel 5.6
     */
    // protected function sendFailedLoginResponse(Request $request)
    // { 
    //     throw ValidationException::withMessages(
    //         [
    //             'login_error' => [trans('auth.login_failed')],
    //         ]
    //     );
    // }

     /**
     * @param Request $request
     * @throws ValidationException
     * this is working for laravel 5.4
     */

    protected function sendFailedLoginResponse(Request $request)
    { 
        return redirect()->back()
            ->withInput($request->only($this->username(), 'identity'))
            ->withErrors(['login_error' => [trans('auth.login_failed')]]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    { 
        $user = \App\User::where(['username' => $request->identity,'status' => 'A'])->first(); 
        if ($user && \Hash::check($request->password, $user->password)) { 
            $this->guard()->login($user, $request->has('remember'));
            return true;
        }
        return false;
        
    }
    
    

}
