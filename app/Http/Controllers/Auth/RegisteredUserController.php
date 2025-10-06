<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Department;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Employee repository instance.
     *
     * @var EmployeeRepositoryInterface
     */
    protected EmployeeRepositoryInterface $employeeRepository;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepositoryInterface $employeeRepository
     */
    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        try {
            $departments = Department::all();
            return view('auth.register', compact('departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the registration page: ' . $e->getMessage());
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        try {
            $user = $this->employeeRepository->createEmployee($request->validated());

            event(new Registered($user));

            Auth::login($user);

            // Redirect based on user role
            if ($user->can('admin')) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('employee.leaves.index');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}
