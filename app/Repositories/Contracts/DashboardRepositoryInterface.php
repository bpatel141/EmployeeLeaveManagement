<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    /**
     * Get count of leave requests by status
     */
    public function getLeaveRequestCountByStatus(string $status): int;

    /**
     * Get count of leave requests by status and date
     */
    public function getLeaveRequestCountByStatusAndDate(string $status, string $date): int;

    /**
     * Get total leave requests count
     */
    public function getTotalLeaveRequestsCount(): int;

    /**
     * Get total employees count
     */
    public function getTotalEmployeesCount(): int;

    /**
     * Get recent pending leave requests
     */
    public function getRecentPendingLeaveRequests(int $limit = 5): Collection;

    /**
     * Get leave requests count by user and status
     */
    public function getLeaveRequestCountByUserAndStatus(int $userId, string $status): int;

    /**
     * Get total approved days for user in a year
     */
    public function getTotalApprovedDaysByUserAndYear(int $userId, int $year): int;

    /**
     * Get recent leave requests by user
     */
    public function getRecentLeaveRequestsByUser(int $userId, int $limit = 5): Collection;
}

