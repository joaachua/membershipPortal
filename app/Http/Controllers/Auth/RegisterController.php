<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function create(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'phone_number' => 'nullable|numeric', 
                'membership' => 'required', 
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'password' => 'required|min:8',
            ];

            $messages = [
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been taken.',
                'phone_number.numeric' => 'The phone number must be numeric.',
                'profile_picture.image' => 'The profile picture must be an image.',
                'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
                'profile_picture.max' => 'The profile picture may not be greater than :max kilobytes.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors()->all(),
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->input('phone_number'),
                'membership' => $request->input('membership'),
                'profile_picture' => $request->hasFile('profile_picture') ? $request->file('profile_picture')->store('profile_pictures') : null,
                'password' => Hash::make($request->password),
            ]);
    
            return response()->json([
                "success" => true,
                "message" => 'User successfully created.',
                "data" => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
