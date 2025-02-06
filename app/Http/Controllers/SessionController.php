<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    protected $redirectTo = '/transactions';

    public function create()
    {
        return view('auth.login');
    }

    public function store()
    {
        //validate
        $att = request()->validate([
            'email'=> ['required', 'email'],
            'password'=>['required'],
        ]);
        //attempt to login
        if(!Auth::attempt($att))
        {
            throw ValidationException::withMessages([
                'email' =>'Sorry, those credentials do not match'
            ]);
        }
        //regenerate the user token
        request()->session()->regenerate();
        //redirect
        return redirect('/');
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/');
    }
}
