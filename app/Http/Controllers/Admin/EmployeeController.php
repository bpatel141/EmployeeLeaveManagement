<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\DeleteEmployeeRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Department;
use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    use ApiResponseTrait;
    
    /**
     * Employee repository instance.
     *
     * @var EmployeeRepositoryInterface
     */
    protected EmployeeRepositoryInterface $employeeRepository;

    /**
     * User repository instance.
     *
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(EmployeeRepositoryInterface $employeeRepository, UserRepositoryInterface $userRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of employees.
     *
     * @return View
     */
    public function index(): View
    {
        try {
            $this->authorize('viewAny', User::class);
            
            $departments = Department::orderBy('name')->get();
            
            return view('admin.employees.index', compact('departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading employees: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return View
     */
    public function create(): View
    {
        try {
            $this->authorize('create', User::class);
            
            $departments = Department::orderBy('name')->get();
            
            return view('admin.employees.create', compact('departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the create form: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        try {
            $this->authorize('update', $user);
            
            $departments = Department::orderBy('name')->get();
            
            return view('admin.employees.edit', compact('user', 'departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the edit form: ' . $e->getMessage());
        }
    }


    /**
     * Get employees data for DataTables.
     *
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);

            $filter = (string) ($request->get('filter') ?? '');
            $period = (string) ($request->get('period') ?? '');

            // Get employees with leave filtering applied
            $query = $this->employeeRepository->getEmployeesWithLeaveFilter($filter, $period);

            return DataTables::of($query)
                ->addColumn('department', function ($user) {
                    return $user->department ? $user->department->name : '-';
                })
                ->editColumn('join_date', function ($user) {
                    return $user->join_date ? $user->join_date->format('Y-m-d') : '-';
                })
                ->addColumn('actions', function ($user) {
                    return view('admin.employees.partials.actions', ['user' => $user])->render();
                })
                ->addColumn('highest_leaves', function ($user) use ($filter, $period) {
                    return $this->employeeRepository->calculateHighestLeaves($user, $filter, $period);
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while loading employee data: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created employee.
     *
     * @param StoreEmployeeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $user = $this->employeeRepository->createEmployee($request->validated());

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified employee.
     *
     * @param UpdateEmployeeRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEmployeeRequest $request, User $user)
    {
        try {
            $this->employeeRepository->updateEmployee($user, $request->validated());

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee (soft delete).
     *
     * @param DeleteEmployeeRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(DeleteEmployeeRequest $request, User $user)
    {
        try {
            $this->userRepository->deleteUser($user);

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->successResponse('Employee deleted successfully.');
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (Exception $e) {
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to delete employee: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }
}
