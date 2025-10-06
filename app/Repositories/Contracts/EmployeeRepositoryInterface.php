<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface EmployeeRepositoryInterface
{
    /**
     * Get all employees with their relationships.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllEmployeesQuery();

    /**
     * Get employees with leave filtering applied.
     *
     * @param string $filter
     * @param string $period
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getEmployeesWithLeaveFilter(string $filter, string $period);

    /**
     * Create a new employee.
     *
     * @param array $data
     * @return User
     */
    public function createEmployee(array $data): User;

    /**
     * Update an existing employee.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateEmployee(User $user, array $data): User;

    /**
     * Delete an employee.
     *
     * @param User $user
     * @return bool
     */
    public function deleteEmployee(User $user): bool;

    /**
     * Find employee by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findEmployee(int $id): ?User;

    /**
     * Attach employee role to user.
     *
     * @param User $user
     * @return void
     */
    public function attachEmployeeRole(User $user): void;

    /**
     * Calculate highest leave taken by employee for a period.
     *
     * @param User $user
     * @param string $filter
     * @param string|null $period
     * @return int
     */
    public function calculateHighestLeaves(User $user, string $filter, ?string $period): int;

    /**
     * Get employee's leave allocations with calculated balance data.
     *
     * @param int $userId
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeaveAllocationsWithBalance(int $userId, int $year);

    /**
     * Get employee's pending leave requests.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingLeaveRequests(int $userId);

    /**
     * Calculate total pending days for a specific leave type.
     *
     * @param int $userId
     * @param int $leaveTypeId
     * @return int
     */
    public function getPendingDaysForLeaveType(int $userId, int $leaveTypeId): int;

    /**
     * Get disabled dates for leave request (dates with pending requests).
     *
     * @param int $userId
     * @return array
     */
    public function getDisabledDatesForLeaveRequest(int $userId): array;

    /**
     * Check if a specific date range conflicts with pending requests.
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeRequestId
     * @return bool
     */
    public function hasDateConflict(int $userId, string $startDate, string $endDate, ?int $excludeRequestId = null): bool;
}

