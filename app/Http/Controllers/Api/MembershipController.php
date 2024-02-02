<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'membership_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'description' => 'nullable',
                'duration_months' => 'required|integer',
                'monthly_fee' => 'required|numeric|min:0',
                'personal_training' => 'nullable|boolean',
                'personal_training_sessions_per_week' => 'nullable|integer|min:0',
                'secure_locker' => 'nullable|boolean',
                'guest_access' => 'nullable|boolean',
                'pay_as_you_go' => 'nullable|boolean',
                'cancellation_policy' => 'nullable'
            ];

            $messages = [
                'name.required' => 'The name field is required.',
                'membership_picture.image' => 'The membership picture must be an image.',
                'membership_picture.mimes' => 'The membership picture must be a file of type: jpeg, png, jpg, gif.',
                'membership_picture.max' => 'The membership picture may not be greater than :max kilobytes.',
                'duration_months.required' => 'The duration months field is required.',
                'duration_months.integer' => 'The duration months must be an integer.',
                'monthly_fee.required' => 'The monthly fee field is required.',
                'monthly_fee.numeric' => 'The monthly fee must be a numeric value.',
                'monthly_fee.min' => 'The monthly fee must be at least :min.',
                'personal_training.boolean' => 'The personal training field must be a boolean value.',
                'personal_training_sessions_per_week.integer' => 'The personal training sessions per week must be an integer.',
                'personal_training_sessions_per_week.min' => 'The personal training sessions per week must be at least :min.',
                'secure_locker.boolean' => 'The secure locker field must be a boolean value.',
                'guest_access.boolean' => 'The guest access field must be a boolean value.',
                'pay_as_you_go.boolean' => 'The pay as you go field must be a boolean value.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $fieldErrors = $validator->errors()->toArray();
                return $this->errorResponse('Validation failed', 422, $fieldErrors);
            }

            DB::beginTransaction();

            try {
                $fileName = "";

                if ($request->hasFile('membership_picture')) {
                    $file = $request->file('membership_picture');
                
                    $request->validate([
                        'membership_picture' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
                    ]);
                
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/memberships'), $fileName);

                    if (!$file->isValid()) {
                        DB::rollBack();
    
                        return $this->errorResponse('Something went wrong.', 500);
                    }
                }

                $membership = Membership::create([
                    'name' => $request->input('name'),
                    'membership_picture' => $fileName,
                    'description' => $request->input('description'),
                    'duration_months' => $request->input('duration_months'),
                    'monthly_fee' => $request->input('monthly_fee'),
                    'personal_training' => $request->input('personal_training'),
                    'personal_training_sessions_per_week' => $request->input('personal_training_sessions_per_week'),
                    'secure_locker' => $request->input('secure_locker'),
                    'guest_access' => $request->input('guest_access'),
                    'pay_as_you_go' => $request->input('pay_as_you_go'),
                    'cancellation_policy' => $request->input('cancellation_policy')
                ]);

                DB::commit();

                return $this->successResponse('Membership created successfully.', 200, $membership);
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

    public function update(Request $request) {
        try {
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'membership_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'description' => 'nullable',
                'duration_months' => 'required|integer',
                'monthly_fee' => 'required|numeric|min:0',
                'personal_training' => 'nullable|boolean',
                'personal_training_sessions_per_week' => 'nullable|integer|min:0',
                'secure_locker' => 'nullable|boolean',
                'guest_access' => 'nullable|boolean',
                'pay_as_you_go' => 'nullable|boolean',
                'cancellation_policy' => 'nullable',
            ];
            
            $messages = [
                'id.required' => 'The id field is required.',
                'name.required' => 'The name field is required.',
                'membership_picture.image' => 'The membership picture must be an image.',
                'membership_picture.mimes' => 'The membership picture must be a file of type: jpeg, png, jpg, gif.',
                'membership_picture.max' => 'The membership picture may not be greater than :max kilobytes.',
                'duration_months.required' => 'The duration months field is required.',
                'duration_months.integer' => 'The duration months must be an integer.',
                'monthly_fee.required' => 'The monthly fee field is required.',
                'monthly_fee.numeric' => 'The monthly fee must be a numeric value.',
                'monthly_fee.min' => 'The monthly fee must be at least :min.',
                'personal_training.boolean' => 'The personal training field must be a boolean value.',
                'personal_training_sessions_per_week.integer' => 'The personal training sessions per week must be an integer.',
                'personal_training_sessions_per_week.min' => 'The personal training sessions per week must be at least :min.',
                'secure_locker.boolean' => 'The secure locker field must be a boolean value.',
                'guest_access.boolean' => 'The guest access field must be a boolean value.',
                'pay_as_you_go.boolean' => 'The pay as you go field must be a boolean value.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                $fieldErrors = $validator->errors()->toArray();
                return $this->errorResponse('Validation failed', 422, $fieldErrors);
            }
            
            DB::beginTransaction();
            
            try {
                $fileName = "";
            
                if ($request->hasFile('membership_picture')) {
                    $file = $request->file('membership_picture');
            
                    $request->validate([
                        'membership_picture' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
                    ]);
            
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/memberships'), $fileName);
            
                    if (!$file->isValid()) {
                        DB::rollBack();
            
                        return $this->errorResponse('Something went wrong.', 500);
                    }
                }
            
                $membership = Membership::find($request->input('id'));
            
                if (!$membership) {
                    DB::rollBack();
            
                    return $this->errorResponse('Membership not found.', 404);
                }
            
                $membership->update([
                    'name' => $request->input('name'),
                    'membership_picture' => $fileName ? $fileName : $membership->membership_picture,
                    'description' => $request->input('description'),
                    'duration_months' => $request->input('duration_months'),
                    'monthly_fee' => $request->input('monthly_fee'),
                    'personal_training' => $request->input('personal_training'),
                    'personal_training_sessions_per_week' => $request->input('personal_training_sessions_per_week'),
                    'secure_locker' => $request->input('secure_locker'),
                    'guest_access' => $request->input('guest_access'),
                    'pay_as_you_go' => $request->input('pay_as_you_go'),
                    'cancellation_policy' => $request->input('cancellation_policy'),
                ]);
            
                DB::commit();
            
                return $this->successResponse('Membership updated successfully.', 200, $membership);
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
    
            $membership = Membership::find($id);
    
            if (!$membership) {
                return $this->errorResponse('Membership not found.', 404);
            }
    
            $membership->delete();
    
            return $this->successResponse('Membership deleted successfully.', 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
    
    public function view(Request $request) {
        try {
            $id = $request->input('id');
    
            $membership = Membership::find($id);
    
            if (!$membership) {
                return $this->errorResponse('Membership not found.', 404);
            }
    
            return $this->successResponse('Membership retrieved successfully.', 200, $membership);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
    
    public function index(Request $request) {
        try {
            $memberships = Membership::all();
    
            if ($memberships->isEmpty()) {
                return $this->successResponse('No memberships found.', 200, []);
            }
    
            return $this->successResponse('Memberships retrieved successfully.', 200, $memberships);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse('Something went wrong.', 500);
        }
    }
}
