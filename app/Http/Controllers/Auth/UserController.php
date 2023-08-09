<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        if (Auth::user() != $user) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email'.$user->id,
            'name' => 'required|min:2'
        ]);
        
        $user->update([
            'email' => $request->email,
            'name' => $request->name,
        ]);

        return redirect()->back();
    }

    public function changePassword(Request $request, User $user)
    {
        if (Auth::user() != $user) {
            abort(403);
        }

        $request->validate([
            'currentPassword' => 'required|min:8',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|min:8'
        ]);

        if (!Hash::check($request->currentPassword, $user->password)) {
            return redirect()->back()->withErrors([
                'currentPassword' => 'Wrong password'
            ]);
        }

        if ($request->newPassword != $request->confirmPassword) {
            return redirect()->back()->withErrors([
                'newPassword' => 'Password does not match'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->confirmPassword)
        ]);

        return redirect()->back();
    }
}
