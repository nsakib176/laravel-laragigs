<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //show register / create form
    public function create()
    {
        return view('users.register');
    }

    // create new user
    public function store(Request $request)
    {
        $formfields = $request->validate([
            'name' => ['required','min:3'],
            'email' => ['required','email', Rule::unique('users','email')],
            'password' => 'required|confirmed|min:6',
        ]);

        //Hash password
        $formfields['password'] = bcrypt($formfields['password']);

        //create user
        $user = User::create($formfields);

        //login user
        auth()->login($user);

        return redirect('/')->with('message','User created and logged in');

    }

    //Logout user
    public function logout(Request $request)
    {
        auth()->logout();

        //invalidate user token and regenerate for future login
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message','You have been logged out.');
    }

    //show login form
    public function login()
    {
        return view('users.login');
    }

    // log in user by authenticating
    public function authenticate(Request $request)
    {
        $formfields = $request->validate([
            'email' => ['required','email'],
            'password' => 'required',
        ]);

        //login attempt
        if(auth()->attempt($formfields)){
            $request->session()->regenerate();

            return redirect('/')->with('message','You are now logged in.');
        }

        //show error message in only email (security risk otherwise)
        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
}
