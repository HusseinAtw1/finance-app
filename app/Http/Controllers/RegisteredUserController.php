<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store()
    {
        // validate the input
        $validatedAttributes = request()->validate([
            'name' => ['required'],
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required', Password::min(8), 'confirmed'], // password confirmation
        ]);

        // create the user
        $user = User::create($validatedAttributes);

        // login the user
        Auth::Login($user);

        // pass the user data to the view
        return redirect('/')->with('user', $user);  // Redirect and pass the user data
    }

}
