<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(Request $request)
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

                \Log::error($e);
                return $this->errorResponse('Something went wrong.', 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'email' => 'email',
                'phone_number' => 'nullable|numeric', 
                'membership' => 'required', 
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'password' => 'nullable|min:8',
            ];

            $messages = [
                'id.required' => 'The id field is required.',
                'name.required' => 'The name field is required.',
                'email.email' => 'The email must be a valid email address.',
                'phone_number.numeric' => 'The phone number must be numeric.',
                'profile_picture.image' => 'The profile picture must be an image.',
                'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
                'profile_picture.max' => 'The profile picture may not be greater than :max kilobytes.',
                'password.min' => 'The password must be at least :min characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $fieldErrors = $validator->errors()->toArray();
                return $this->errorResponse('Validation failed', 422, $fieldErrors);
            }

            DB::beginTransaction();

            try {
                $user = User::findOrFail($request->id);

                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                
                    $request->validate([
                        'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
                
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/profiles'), $fileName);
                    $user->profile_picture = $fileName;
                }

                if ($request->has('name')) {
                    $user->name = $request->name;
                }

                if ($request->has('email')) {
                    $user->email = $request->email;
                }

                if ($request->has('phone_number')) {
                    $user->phone_number = $request->phone_number;
                }

                if ($request->has('membership')) {
                    $user->membership = $request->membership;
                }

                if ($request->has('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                DB::commit();

                return $this->successResponse('User updated successfully.', 200, $user);
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error($e);
                return $this->errorResponse('Something went wrong.', 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    public function destroy(Request $request) {
        try {
            $id = $request->input('id');
    
            $user = User::find($id);
    
            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }
    
            $user->delete();
    
            return $this->successResponse('User deleted successfully.', 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
    
    public function view(Request $request) {
        try {
            $id = $request->input('id');
    
            $user = User::find($id);
    
            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }
    
            return $this->successResponse('User retrieved successfully.', 200, $user);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
    
    public function index(Request $request) {
        try {
            $users = User::all();
    
            if ($users->isEmpty()) {
                return $this->successResponse('No users found.', 200, []);
            }
    
            return $this->successResponse('Users retrieved successfully.', 200, $users);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    public function loggedInUpdate(Request $request)
    {
        try {
            $user = Auth::user(); 
            
            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $rules = [
                'email' => 'email',
                'phone_number' => 'nullable|numeric',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'password' => 'nullable|min:8',
            ];

            $messages = [
                'email.email' => 'The email must be a valid email address.',
                'phone_number.numeric' => 'The phone number must be numeric.',
                'profile_picture.image' => 'The profile picture must be an image.',
                'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
                'profile_picture.max' => 'The profile picture may not be greater than :max kilobytes.',
                'password.min' => 'The password must be at least :min characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $fieldErrors = $validator->errors()->toArray();
                return $this->errorResponse('Validation failed', 422, $fieldErrors);
            }

            DB::beginTransaction();

            try {
                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                
                    $request->validate([
                        'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
                
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/profiles'), $fileName);
                    $user->profile_picture = $fileName;
                }

                if ($request->has('name')) {
                    $user->name = $request->name;
                }

                if ($request->has('email')) {
                    $user->email = $request->email;
                }

                if ($request->has('phone_number')) {
                    $user->phone_number = $request->phone_number;
                }

                if ($request->has('membership')) {
                    $user->membership = $request->membership;
                }

                if ($request->has('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                DB::commit();

                return $this->successResponse('Profile updated successfully.', 200, $user);
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error($e);
                return $this->errorResponse('Something went wrong.', 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    public function loggedInView(Request $request)
    {
        try {
            $user = Auth::user(); 

            if (!$user) {
                return $this->errorResponse('Profile not found.', 404);
            }

            return $this->successResponse('Profile retrieved successfully.', 200, $user);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();
    
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_new_password' => 'required|same:new_password',
            ], [
                'confirm_new_password.same' => 'The new password and confirm new password must match.',
            ]);
    
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', 400);
            }
    
            $user->password = Hash::make($request->new_password);
            $user->save();
    
            return $this->successResponse('Password changed successfully', 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
}
