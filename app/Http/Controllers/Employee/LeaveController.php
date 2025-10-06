<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest\StoreLeaveRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\LeaveType;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class LeaveController extends Controller
{
    use ApiResponseTrait;
    
    protected LeaveRequestRepositoryInterface $leaveRequestRepository;
    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        LeaveRequestRepositoryInterface $leaveRequestRepository,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->leaveRequestRepository = $leaveRequestRepository;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display employee's leave management page
     */
    public function index(Request $request): View
    {
        try {
            $user = $request->user();
            $leaveTypes = LeaveType::all();
            $currentYear = now()->year;
            
            // Get leave allocations with calculated balance data from repository
            $allocations = $this->employeeRepository->getLeaveAllocationsWithBalance($user->id, $currentYear);
                
            // Get pending requests for balance calculation
            $pendingRequests = $this->employeeRepository->getPendingLeaveRequests($user->id);

            // Get disabled dates for date picker
            $disabledDates = $this->employeeRepository->getDisabledDatesForLeaveRequest($user->id);
                
            return view('employee.leaves.index', compact('leaveTypes', 'allocations', 'currentYear', 'pendingRequests', 'disabledDates'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading leave management page: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new leave request
     */
    public function create(Request $request): View
    {
        try {
            $user = $request->user();
            $leaveTypes = LeaveType::all();
            $currentYear = now()->year;
            
            // Get leave allocations with calculated balance data from repository
            $allocations = $this->employeeRepository->getLeaveAllocationsWithBalance($user->id, $currentYear);
                
            // Get pending requests for balance calculation
            $pendingRequests = $this->employeeRepository->getPendingLeaveRequests($user->id);

            // Get disabled dates for date picker
            $disabledDates = $this->employeeRepository->getDisabledDatesForLeaveRequest($user->id);
                
            return view('employee.leaves.create', compact('leaveTypes', 'allocations', 'currentYear', 'pendingRequests', 'disabledDates'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the create form: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's leave requests data for DataTables
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = $this->leaveRequestRepository->getAllLeaveRequestsQuery()
                ->where('user_id', $user->id);

            return DataTables::of($query)
                ->addColumn('leave_type', fn ($lr) => $lr->leaveType->name)
                ->editColumn('start_date', fn ($lr) => $lr->start_date->format('Y-m-d'))
                ->editColumn('end_date', fn ($lr) => $lr->end_date->format('Y-m-d'))
                ->editColumn('created_at', fn ($lr) => $lr->created_at->format('M d, Y \a\t g:i A'))
                ->addColumn('status_badge', function ($lr) {
                    $badgeClass = match ($lr->status) {
                        'pending' => 'bg-yellow-200 text-yellow-800',
                        'approved' => 'bg-green-200 text-green-800',
                        'rejected' => 'bg-red-200 text-red-800',
                        default => 'bg-gray-200 text-gray-800',
                    };
                    return "<span class='px-2 py-1 rounded-full text-xs font-semibold {$badgeClass}'>" . ucfirst($lr->status) . "</span>";
                })
                ->addColumn('actions', function ($lr) {
                    return view('employee.leaves.partials.actions', ['leaveRequest' => $lr])->render();
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while loading leave request data: ' . $e->getMessage());
        }
    }

    /**
     * Store a new leave request
     */
    public function store(StoreLeaveRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;
            
            $leaveRequest = $this->leaveRequestRepository->createLeaveRequest($data);
            
            return redirect()->route('employee.leaves.index')
                ->with('success', 'Leave request submitted successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit leave request: ' . $e->getMessage());
        }
    }

    /**
     * Delete a pending leave request
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $deleted = $this->leaveRequestRepository->deleteLeaveRequest($id, $user->id);
            
            if ($deleted) {
                return $this->successResponse('Leave request deleted successfully.');
            } else {
                return $this->errorResponse('Failed to delete leave request.');
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Show employee's leave allocations
     */
    public function allocations(Request $request): View
    {
        try {
            $user = $request->user();
            $currentYear = now()->year;
            
            // Get leave allocations with calculated balance data from repository
            $allocations = $this->employeeRepository->getLeaveAllocationsWithBalance($user->id, $currentYear);

            return view('employee.leaves.allocations', compact('allocations', 'currentYear'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading leave allocations: ' . $e->getMessage());
        }
    }
}
