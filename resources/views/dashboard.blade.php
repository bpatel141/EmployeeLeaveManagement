<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $isAdmin ? __('Admin Dashboard') : __('Employee Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($isAdmin)
                <!-- Admin Dashboard -->
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Pending Leaves Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Pending Approvals</p>
                                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_leaves'] }}</p>
                                </div>
                                <div class="bg-yellow-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $stats['pending_today'] }} received today</p>
                        </div>
                    </div>

                    <!-- Approved Leaves Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Approved Leaves</p>
                                    <p class="text-3xl font-bold text-green-600">{{ $stats['approved_leaves'] }}</p>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Total approved requests</p>
                        </div>
                    </div>

                    <!-- Rejected Leaves Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Rejected Leaves</p>
                                    <p class="text-3xl font-bold text-red-600">{{ $stats['rejected_leaves'] }}</p>
                                </div>
                                <div class="bg-red-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Total rejected requests</p>
                        </div>
                    </div>

                    <!-- Total Employees Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Employees</p>
                                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_employees'] }}</p>
                                </div>
                                <div class="bg-blue-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Active employees</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Pending Leave Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Pending Leave Requests</h3>
                            <a href="{{ route('admin.leave-requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All →
                            </a>
                        </div>
                        
                        @if($recentPendingLeaves->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentPendingLeaves as $leave)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $leave->user->name }}</div>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="text-sm text-gray-600">{{ $leave->leaveType->name }}</span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                                                    {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                                    <span class="text-sm font-semibold text-gray-900">{{ $leave->days }}</span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $leave->created_at->diffForHumans() }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                    <a href="{{ route('admin.leave-requests.index') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                        Review
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No pending leave requests</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('admin.leave-requests.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span class="text-blue-700 font-medium">Manage Leave Requests</span>
                            </a>
                            
                            <a href="{{ route('admin.employees.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-green-700 font-medium">Manage Employees</span>
                            </a>
                            
                        </div>
                    </div>
                </div>

            @else
                <!-- Employee Dashboard -->
                
                <!-- Statistics Cards for Employee -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 font-medium">Pending Requests</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['my_pending'] }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 font-medium">Approved</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['my_approved'] }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 font-medium">Rejected</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['my_rejected'] }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 font-medium">Days Taken ({{ now()->year }})</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_days_taken'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employee's Recent Leave Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">My Recent Leave Requests</h3>
                        
                        @if($myRecentLeaves->count() > 0)
                            <div class="space-y-3">
                                @foreach($myRecentLeaves as $leave)
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3">
                                                    <span class="font-semibold text-gray-800">{{ $leave->leaveType->name }}</span>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                        {{ ucfirst($leave->status) }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }} ({{ $leave->days }} days)
                                                </p>
                                            </div>
                                            <div class="text-right text-sm text-gray-500">
                                                {{ $leave->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No leave requests yet</p>
                            </div>
                        @endif
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
