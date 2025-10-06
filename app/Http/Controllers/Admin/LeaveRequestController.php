<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest\ApproveLeaveRequest;
use App\Http\Requests\LeaveRequest\RejectLeaveRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\LeaveRequest;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class LeaveRequestController extends Controller
{
    use ApiResponseTrait;
    /**
     * LeaveRequest repository instance.
     *
     * @var LeaveRequestRepositoryInterface
     */
    protected LeaveRequestRepositoryInterface $leaveRequestRepository;

    /**
     * Create a new controller instance.
     *
     * @param LeaveRequestRepositoryInterface $leaveRequestRepository
     */
    public function __construct(LeaveRequestRepositoryInterface $leaveRequestRepository)
    {
        $this->leaveRequestRepository = $leaveRequestRepository;
    }

    /**
     * Display a listing of leave requests.
     *
     * @return View
     */
    public function index(): View
    {
        try {
            // Authorization handled by route middleware 'can:admin'
            return view('admin.leave-requests.index');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading leave requests: ' . $e->getMessage());
        }
    }

    /**
     * Get leave requests data for DataTables.
     *
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        try {
            $status = $request->get('status', '');
            
            $query = $this->leaveRequestRepository->getAllLeaveRequestsQuery();

            // Filter by status if provided
            if (!empty($status)) {
                $query->where('status', $status);
            }

            return DataTables::of($query)
                ->addColumn('employee_name', function ($leaveRequest) {
                    return $leaveRequest->user ? $leaveRequest->user->name : '-';
                })
                ->addColumn('leave_type_name', function ($leaveRequest) {
                    return $leaveRequest->leaveType ? $leaveRequest->leaveType->name : '-';
                })
                ->editColumn('start_date', function ($leaveRequest) {
                    return $leaveRequest->start_date ? $leaveRequest->start_date->format('Y-m-d') : '-';
                })
                ->editColumn('end_date', function ($leaveRequest) {
                    return $leaveRequest->end_date ? $leaveRequest->end_date->format('Y-m-d') : '-';
                })
                ->addColumn('status_badge', function ($leaveRequest) {
                    $colors = [
                        'pending' => 'bg-yellow-500',
                        'approved' => 'bg-green-500',
                        'rejected' => 'bg-red-500',
                    ];
                    $color = $colors[$leaveRequest->status] ?? 'bg-gray-500';
                    
                    return '<span style="padding: 4px 12px; background-color: ' . 
                           ($leaveRequest->status === 'pending' ? '#EAB308' : 
                           ($leaveRequest->status === 'approved' ? '#22C55E' : '#EF4444')) . 
                           '; color: white; border-radius: 12px; font-size: 12px; font-weight: 600;">' . 
                           ucfirst($leaveRequest->status) . 
                           '</span>';
                })
                ->addColumn('actions', function ($leaveRequest) {
                    return view('admin.leave-requests.partials.actions', ['leaveRequest' => $leaveRequest])->render();
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while loading leave request data: ' . $e->getMessage());
        }
    }

    /**
     * Approve a leave request.
     *
     * @param ApproveLeaveRequest $request
     * @param LeaveRequest $leaveRequest
     * @return JsonResponse
     */
    public function approve(ApproveLeaveRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        try {
            if ($leaveRequest->status !== 'pending') {
                return $this->errorResponse('Only pending leave requests can be approved.');
            }

            $this->leaveRequestRepository->approveLeaveRequest(
                $leaveRequest,
                $request->input('admin_comment')
            );

            return $this->successResponse('Leave request approved successfully.');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while approving the leave request: ' . $e->getMessage());
        }
    }

    /**
     * Reject a leave request.
     *
     * @param RejectLeaveRequest $request
     * @param LeaveRequest $leaveRequest
     * @return JsonResponse
     */
    public function reject(RejectLeaveRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        try {
            if ($leaveRequest->status !== 'pending') {
                return $this->errorResponse('Only pending leave requests can be rejected.');
            }

            $this->leaveRequestRepository->rejectLeaveRequest(
                $leaveRequest,
                $request->input('admin_comment')
            );

            return $this->successResponse('Leave request rejected successfully.');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while rejecting the leave request: ' . $e->getMessage());
        }
    }
}

