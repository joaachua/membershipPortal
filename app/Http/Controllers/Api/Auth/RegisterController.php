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

    protected function register(Request $request)
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
                $fieldErrors = $validator->errors()->toArray();
                return $this->errorResponse('Validation failed', 422, $fieldErrors);
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

                return $this->successResponse('User created successfully.', 200, $user);
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->errorResponse('Something went wrong.', 500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    protected function login(Request $request) {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if (Auth::attempt($credentials)) {
                $data = [
                    'token' => Auth::user()->createToken('api-token')->plainTextToken,
                    'user'  => Auth::user()
                ];
    
                return $this->successResponse('User created successfully.', 200, $data);
            } else {
                throw new AuthenticationException();
            }
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (AuthenticationException $e) {
            return $this->errorResponse('Invalid credentials.', 401);
        } catch (\Exception $e) {
            \Log::error($e);
    
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
}
