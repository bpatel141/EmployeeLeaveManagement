<?php

namespace App\Repositories;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Get all employees with their relationships.
     *
     * @return Builder
     */
    public function getAllEmployeesQuery(): Builder
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })->with(['roles', 'department']);
    }

    /**
     * Get employees with leave filtering applied.
     *
     * @param string $filter
     * @param string $period
     * @return Builder
     */
    public function getEmployeesWithLeaveFilter(string $filter, string $period): Builder
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })->with(['roles', 'department']);

        // If no filter is applied, return all employees
        if (empty($filter) || empty($period)) {
            return $query;
        }

        // Only show employees who have approved leave requests in the specified period
        $query->whereHas('leaveRequests', function ($q) use ($filter, $period) {
            $q->where('status', 'approved');
            
            if ($filter === 'monthly' && strpos($period, '-') !== false) {
                [$year, $month] = explode('-', $period);
                $year = (int) $year;
                $month = (int) $month;
                
                $q->whereYear('start_date', $year)
                  ->whereMonth('start_date', $month);
            } elseif ($filter === 'yearly') {
                $q->whereYear('start_date', (int) $period);
            }
        });

        return $query;
    }

    /**
     * Create a new employee.
     *
     * @param array $data
     * @return User
     */
    public function createEmployee(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $name = $data['name'];
            $generatedPassword = ucfirst($name) . '@123';
            $data['password'] = Hash::make($generatedPassword);

            $user = User::create($data);

            $user->plain_password = $generatedPassword;

            $this->attachEmployeeRole($user);

            $this->allocateYearlyLeaves($user);

            return $user->load(['roles', 'department']);
        });
    }

    /**
     * Allocate yearly leaves to an employee.
     *
     * @param User $user
     * @return void
     */
    protected function allocateYearlyLeaves(User $user): void
    {
        $currentYear = now()->year;

        // Get leave types
        $sickLeave = LeaveType::where('name', 'Sick')->first();
        $casualLeave = LeaveType::where('name', 'Casual')->first();

        // Allocate Sick Leave (7 days per year)
        if ($sickLeave) {
            LeaveAllocation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $sickLeave->id,
                    'year' => $currentYear,
                ],
                [
                    'total_allocated' => 7,
                    'remaining' => 7,
                ]
            );
        }

        // Allocate Casual Leave (30 days per year)
        if ($casualLeave) {
            LeaveAllocation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $casualLeave->id,
                    'year' => $currentYear,
                ],
                [
                    'total_allocated' => 30,
                    'remaining' => 30,
                ]
            );
        }
    }

    /**
     * Update an existing employee.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateEmployee(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);
            return $user->fresh(['roles', 'department']);
        });
    }

    /**
     * Delete an employee.
     *
     * @param User $user
     * @return bool
     */
    public function deleteEmployee(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return $user->delete();
        });
    }

    /**
     * Find employee by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findEmployee(int $id): ?User
    {
        return User::with(['roles', 'department'])->find($id);
    }

    /**
     * Attach employee role to user.
     *
     * @param User $user
     * @return void
     */
    public function attachEmployeeRole(User $user): void
    {
        $role = Role::firstOrCreate(['name' => 'employee']);
        
        if (!$user->roles()->where('name', 'employee')->exists()) {
            $user->roles()->attach($role->id);
        }
    }

    /**
     * Calculate highest leave taken by employee for a period.
     *
     * @param User $user
     * @param string $filter
     * @param string|null $period
     * @return int
     */
    public function calculateHighestLeaves(User $user, string $filter, ?string $period): int
    {
        $query = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved');

        // Only apply filters if both filter and period are provided and not empty
        if (!empty($filter) && !empty($period)) {
            if ($filter === 'monthly' && strpos($period, '-') !== false) {
                // $period expected YYYY-MM
                [$year, $month] = explode('-', $period);
                // Remove leading zeros and ensure proper format
                $year = (int) $year;
                $month = (int) $month;
                
                $query->whereYear('start_date', $year)
                      ->whereMonth('start_date', $month);
            } elseif ($filter === 'yearly') {
                $query->whereYear('start_date', (int) $period);
            }
        }

        return (int) $query->sum('days');
    }

    /**
     * Get employee's leave allocations with calculated balance data.
     *
     * @param int $userId
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeaveAllocationsWithBalance(int $userId, int $year)
    {
        $allocations = LeaveAllocation::where('user_id', $userId)
            ->where('year', $year)
            ->with('leaveType')
            ->get();

        // Calculate pending days for each allocation
        $pendingRequests = LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->get(['leave_type_id', 'days']);

        return $allocations->map(function ($allocation) use ($pendingRequests) {
            // Calculate pending days for this leave type
            $pendingDays = $pendingRequests
                ->where('leave_type_id', $allocation->leave_type_id)
                ->sum('days');

            // Calculate effective remaining (allocated - used - pending)
            $effectiveRemaining = $allocation->remaining - $pendingDays;
            $totalUsed = $allocation->total_allocated - $allocation->remaining;
            $totalPending = $pendingDays;

            // Add calculated fields to the allocation
            $allocation->effective_remaining = $effectiveRemaining;
            $allocation->total_used = $totalUsed;
            $allocation->total_pending = $totalPending;

            return $allocation;
        });
    }

    /**
     * Get employee's pending leave requests.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingLeaveRequests(int $userId)
    {
        return LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->get(['leave_type_id', 'days']);
    }

    /**
     * Calculate total pending days for a specific leave type.
     *
     * @param int $userId
     * @param int $leaveTypeId
     * @return int
     */
    public function getPendingDaysForLeaveType(int $userId, int $leaveTypeId): int
    {
        return LeaveRequest::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'pending')
            ->sum('days');
    }

    /**
     * Get disabled dates for leave request (dates with pending requests).
     *
     * @param int $userId
     * @return array
     */
    public function getDisabledDatesForLeaveRequest(int $userId): array
    {
        $pendingRequests = LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->get(['start_date', 'end_date']);

        $disabledDates = [];

        foreach ($pendingRequests as $request) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            
            // Add all dates in the range to disabled dates
            while ($startDate->lte($endDate)) {
                $disabledDates[] = $startDate->format('Y-m-d');
                $startDate->addDay();
            }
        }

        return array_unique($disabledDates);
    }

    /**
     * Check if a specific date range conflicts with pending requests.
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeRequestId
     * @return bool
     */
    public function hasDateConflict(int $userId, string $startDate, string $endDate, ?int $excludeRequestId = null): bool
    {
        $query = LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeRequestId) {
            $query->where('id', '!=', $excludeRequestId);
        }

        return $query->exists();
    }
}

