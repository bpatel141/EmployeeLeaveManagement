<?php

namespace App\Http\Requests\LeaveRequest;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Employees can create their own leave requests
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $leaveTypeId = $this->input('leave_type_id');
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');
            $userId = $this->user()->id;
            
            if ($leaveTypeId && $startDate && $endDate) {
                // Calculate requested days
                $requestedDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
                
                // Get current year
                $currentYear = now()->year;
                
                // Check available leave allocation
                $allocation = LeaveAllocation::where('user_id', $userId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('year', $currentYear)
                    ->first();
                
                if (!$allocation) {
                    $validator->errors()->add('leave_type_id', 'You do not have any leave allocation for this leave type.');
                } else {
                    // Get pending days for this leave type
                    $pendingDays = LeaveRequest::where('user_id', $userId)
                        ->where('leave_type_id', $leaveTypeId)
                        ->where('status', 'pending')
                        ->sum('days');
                    
                    $effectiveRemaining = $allocation->remaining - $pendingDays;
                    
                    if ($effectiveRemaining < $requestedDays) {
                        $leaveType = LeaveType::find($leaveTypeId);
                        $validator->errors()->add('days', "You only have {$effectiveRemaining} days available for {$leaveType->name} leave (including pending requests). You are requesting {$requestedDays} days.");
                    }
                }

                // Check for date conflicts with pending requests
                $employeeRepository = app(EmployeeRepositoryInterface::class);
                if ($employeeRepository->hasDateConflict($userId, $startDate, $endDate)) {
                    $validator->errors()->add('start_date', 'The selected date range conflicts with your existing pending leave requests.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'leave_type_id.required' => 'Please select a leave type.',
            'leave_type_id.exists' => 'The selected leave type is invalid.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or later.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
            'reason.required' => 'Please provide a reason for your leave request.',
            'reason.min' => 'Reason must be at least 10 characters long.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }
}
