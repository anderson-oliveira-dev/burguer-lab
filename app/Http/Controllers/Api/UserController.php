<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function update(Request $request){
        $user = $request->user();

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function updatePassword(Request $request){
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password'      => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['A senha atual está incorreta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }
}
