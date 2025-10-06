<?php

namespace App\Repositories;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{
    /**
     * Get all leave requests with relationships.
     *
     * @return Builder
     */
    public function getAllLeaveRequestsQuery(): Builder
    {
        return LeaveRequest::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get pending leave requests.
     *
     * @return Builder
     */
    public function getPendingLeaveRequestsQuery(): Builder
    {
        return LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Find leave request by ID.
     *
     * @param int $id
     * @return LeaveRequest|null
     */
    public function findLeaveRequest(int $id): ?LeaveRequest
    {
        return LeaveRequest::with(['user', 'leaveType'])->find($id);
    }

    /**
     * Approve a leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @param string|null $adminComment
     * @return LeaveRequest
     */
    public function approveLeaveRequest(LeaveRequest $leaveRequest, ?string $adminComment): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $adminComment) {
            // Update leave request status
            $leaveRequest->update([
                'status' => 'approved',
                'admin_comment' => $adminComment,
                'approved_at' => now(),
            ]);

            // Deduct from leave allocation
            $currentYear = now()->year;
            $allocation = LeaveAllocation::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $currentYear)
                ->first();

            if ($allocation && $allocation->remaining >= $leaveRequest->days) {
                $allocation->decrement('remaining', $leaveRequest->days);
            }

            return $leaveRequest->fresh(['user', 'leaveType']);
        });
    }

    /**
     * Reject a leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @param string $adminComment
     * @return LeaveRequest
     */
    public function rejectLeaveRequest(LeaveRequest $leaveRequest, string $adminComment): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $adminComment) {
            // Update leave request status
            $leaveRequest->update([
                'status' => 'rejected',
                'admin_comment' => $adminComment,
            ]);

            // If the leave request was pending, we need to credit back the days
            // to the employee's allocation (since they were never actually deducted)
            // But first, let's check if this was a pending request
            if ($leaveRequest->getOriginal('status') === 'pending') {
                $currentYear = now()->year;
                $allocation = LeaveAllocation::where('user_id', $leaveRequest->user_id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->where('year', $currentYear)
                    ->first();

                if ($allocation) {
                    // Since this was a pending request, the days were never deducted
                    // We don't need to do anything here - the days remain available
                    // The rejection just changes the status from pending to rejected
                }
            }

            return $leaveRequest->fresh(['user', 'leaveType']);
        });
    }

    /**
     * Create a new leave request.
     *
     * @param array $data
     * @return LeaveRequest
     */
    public function createLeaveRequest(array $data): LeaveRequest
    {
        // Calculate days between start and end date
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $days = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end days

        $data['days'] = $days;
        $data['status'] = 'pending';

        return LeaveRequest::create($data);
    }

    /**
     * Delete a pending leave request (only if status is pending).
     *
     * @param int $leaveRequestId
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function deleteLeaveRequest(int $leaveRequestId, int $userId): bool
    {
        return DB::transaction(function () use ($leaveRequestId, $userId) {
            $leaveRequest = LeaveRequest::where('id', $leaveRequestId)
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->first();

            if (!$leaveRequest) {
                throw new Exception('Leave request not found or cannot be deleted.');
            }

            return $leaveRequest->delete();
        });
    }
}

