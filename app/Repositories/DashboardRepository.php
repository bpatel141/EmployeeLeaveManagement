<?php

namespace App\Repositories;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get count of leave requests by status
     */
    public function getLeaveRequestCountByStatus(string $status): int
    {
        return LeaveRequest::where('status', $status)->count();
    }

    /**
     * Get count of leave requests by status and date
     */
    public function getLeaveRequestCountByStatusAndDate(string $status, string $date): int
    {
        return LeaveRequest::where('status', $status)
            ->whereDate('created_at', $date)
            ->count();
    }

    /**
     * Get total leave requests count
     */
    public function getTotalLeaveRequestsCount(): int
    {
        return LeaveRequest::count();
    }

    /**
     * Get total employees count
     */
    public function getTotalEmployeesCount(): int
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })->count();
    }

    /**
     * Get recent pending leave requests
     */
    public function getRecentPendingLeaveRequests(int $limit = 5): Collection
    {
        return LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get leave requests count by user and status
     */
    public function getLeaveRequestCountByUserAndStatus(int $userId, string $status): int
    {
        return LeaveRequest::where('user_id', $userId)
            ->where('status', $status)
            ->count();
    }

    /**
     * Get total approved days for user in a year
     */
    public function getTotalApprovedDaysByUserAndYear(int $userId, int $year): int
    {
        return (int) LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('days');
    }

    /**
     * Get recent leave requests by user
     */
    public function getRecentLeaveRequestsByUser(int $userId, int $limit = 5): Collection
    {
        return LeaveRequest::with('leaveType')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

