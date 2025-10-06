@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">My Leave Allocations</h2>
            <p class="text-gray-600">View your leave allocations for {{ $currentYear }}</p>
        </div>
        <a href="{{ route('employee.leaves.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Leave Requests
        </a>
    </div>

    <!-- Leave Allocations Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @forelse($allocations as $allocation)
            <div class="bg-white rounded shadow">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $allocation->leaveType->name }} Leave</h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            {{ $currentYear }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Total Allocated -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Total Allocated</span>
                            <span class="text-lg font-bold text-gray-900">{{ $allocation->total_allocated }} days</span>
                        </div>
                        
                        <!-- Available -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Available</span>
                            <span class="text-lg font-bold text-green-600">{{ $allocation->effective_remaining }} days</span>
                        </div>
                        
                        <!-- Used -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Used</span>
                            <span class="text-lg font-bold text-red-600">{{ $allocation->total_used }} days</span>
                        </div>
                        
                        @if($allocation->total_pending > 0)
                        <!-- Pending -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Pending</span>
                            <span class="text-lg font-bold text-orange-600">{{ $allocation->total_pending }} days</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Usage</span>
                            <span>{{ round((($allocation->total_used + $allocation->total_pending) / $allocation->total_allocated) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ (($allocation->total_used + $allocation->total_pending) / $allocation->total_allocated) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Leave Allocations</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any leave allocations for {{ $currentYear }} yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Request Leave
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Summary Card -->
    @if($allocations->count() > 0)
        <div class="bg-white rounded shadow">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary for {{ $currentYear }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $allocations->sum('total_allocated') }}</p>
                        <p class="text-sm text-gray-600">Total Days Allocated</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $allocations->sum('effective_remaining') }}</p>
                        <p class="text-sm text-gray-600">Days Available</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-red-600">{{ $allocations->sum('total_used') }}</p>
                        <p class="text-sm text-gray-600">Days Used</p>
                    </div>
                    @if($allocations->sum('total_pending') > 0)
                    <div class="text-center">
                        <p class="text-3xl font-bold text-orange-600">{{ $allocations->sum('total_pending') }}</p>
                        <p class="text-sm text-gray-600">Days Pending</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
