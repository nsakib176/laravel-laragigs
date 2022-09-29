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
}
