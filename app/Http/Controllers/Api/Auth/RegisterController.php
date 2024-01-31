<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

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

            DB::beginTransaction();

            try {
                $fileName = "";

                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                
                    $request->validate([
                        'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
                
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/profiles'), $fileName);
                }

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->input('phone_number'),
                    'membership' => $request->input('membership'),
                    'profile_picture' => $fileName,
                    'password' => Hash::make($request->password),
                ]);

                DB::commit();

                return response()->json([
                    "success" => true,
                    "message" => 'User successfully created.',
                    "data" => $user
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    "success" => false,
                    "message" => 'Something went wrong.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'Something went wrong.'
            ], 500);
        }
    }

    protected function login(Request $request) {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('api-token')->plainTextToken;
    
                return response()->json([
                    'success' => true,
                    'message' => 'Login successfully.',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                    ],
                ]);
            } else {
                throw new AuthenticationException();
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        } catch (\Exception $e) {
            \Log::error($e);
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
