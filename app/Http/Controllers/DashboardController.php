<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        try {
            $user = $request->user();
            $isAdmin = $user->can('admin');

            if ($isAdmin) {
                return $this->adminDashboard($user);
            }

            return $this->employeeDashboard($user);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Admin Dashboard
     */
    protected function adminDashboard($user): View
    {
        try {
            $isAdmin = $user->can('admin');

            $stats = [
                'pending_leaves' => $this->dashboardRepository->getLeaveRequestCountByStatus('pending'),
                'approved_leaves' => $this->dashboardRepository->getLeaveRequestCountByStatus('approved'),
                'rejected_leaves' => $this->dashboardRepository->getLeaveRequestCountByStatus('rejected'),
                'total_employees' => $this->dashboardRepository->getTotalEmployeesCount(),
                'total_leave_requests' => $this->dashboardRepository->getTotalLeaveRequestsCount(),
                'pending_today' => $this->dashboardRepository->getLeaveRequestCountByStatusAndDate('pending', today()->toDateString()),
            ];

            $recentPendingLeaves = $this->dashboardRepository->getRecentPendingLeaveRequests(5);

            return view('dashboard', compact('stats', 'recentPendingLeaves', 'isAdmin'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading admin dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Employee Dashboard
     */
    protected function employeeDashboard($user): View
    {
        try {
            $isAdmin = $user->can('admin');

            $stats = [
                'my_pending' => $this->dashboardRepository->getLeaveRequestCountByUserAndStatus($user->id, 'pending'),
                'my_approved' => $this->dashboardRepository->getLeaveRequestCountByUserAndStatus($user->id, 'approved'),
                'my_rejected' => $this->dashboardRepository->getLeaveRequestCountByUserAndStatus($user->id, 'rejected'),
                'total_days_taken' => $this->dashboardRepository->getTotalApprovedDaysByUserAndYear($user->id, now()->year),
            ];

            $myRecentLeaves = $this->dashboardRepository->getRecentLeaveRequestsByUser($user->id, 5);

            return view('dashboard', compact('stats', 'myRecentLeaves', 'isAdmin'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading employee dashboard: ' . $e->getMessage());
        }
    }
}
