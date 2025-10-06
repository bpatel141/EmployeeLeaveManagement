<?php

namespace App\Repositories\Contracts;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Builder;

interface LeaveRequestRepositoryInterface
{
    /**
     * Get all leave requests with relationships.
     *
     * @return Builder
     */
    public function getAllLeaveRequestsQuery(): Builder;

    /**
     * Get pending leave requests.
     *
     * @return Builder
     */
    public function getPendingLeaveRequestsQuery(): Builder;

    /**
     * Find leave request by ID.
     *
     * @param int $id
     * @return LeaveRequest|null
     */
    public function findLeaveRequest(int $id): ?LeaveRequest;

    /**
     * Approve a leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @param string|null $adminComment
     * @return LeaveRequest
     */
    public function approveLeaveRequest(LeaveRequest $leaveRequest, ?string $adminComment): LeaveRequest;

    /**
     * Reject a leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @param string $adminComment
     * @return LeaveRequest
     */
    public function rejectLeaveRequest(LeaveRequest $leaveRequest, string $adminComment): LeaveRequest;

    /**
     * Create a new leave request.
     *
     * @param array $data
     * @return LeaveRequest
     */
    public function createLeaveRequest(array $data): LeaveRequest;

    /**
     * Delete a pending leave request (only if status is pending).
     *
     * @param int $leaveRequestId
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function deleteLeaveRequest(int $leaveRequestId, int $userId): bool;
}

