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
        $validatedAttributes = request()->validate([
            'name'     => ['required'],
            'email'    => ['required', 'email', 'max:255'],
            'timezone' => ['required', function($attribute, $value, $fail) {
                if (!in_array($value, \DateTimeZone::listIdentifiers())) {
                    $fail('The selected timezone is invalid.');
                }
            }],
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user = User::create($validatedAttributes);

        Auth::Login($user);

        return redirect('/')->with('user', $user);
    }

}
