@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">My Leave Management</h2>
            <p class="text-gray-600">Manage your leave requests and view your leave history</p>
        </div>
        <a href="{{ route('employee.leaves.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow flex items-center hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Leave Request
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600" id="pending-count">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-green-600" id="approved-count">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-2xl font-bold text-red-600" id="rejected-count">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Days Taken ({{ date('Y') }})</p>
                    <p class="text-2xl font-bold text-blue-600" id="days-taken-count">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Balance Overview -->
    <div class="bg-white rounded shadow p-4 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Leave Balance for {{ $currentYear }}</h3>
                <p class="text-gray-600">Your current leave allocation and usage</p>
            </div>
            <a href="{{ route('employee.leaves.allocations') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                View Details
            </a>
        </div>
        
        @if($allocations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($allocations as $allocation)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900">{{ $allocation->leaveType->name }} Leave</h4>
                            <span class="text-sm text-gray-600">{{ $allocation->effective_remaining }}/{{ $allocation->total_allocated }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($allocation->effective_remaining / $allocation->total_allocated) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>{{ $allocation->effective_remaining }} available</span>
                            <span>{{ $allocation->total_used }} used</span>
                        </div>
                        @if($allocation->total_pending > 0)
                            <div class="text-xs text-orange-600 mt-1">
                                {{ $allocation->total_pending }} days pending approval
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-gray-500">No leave allocations found for {{ $currentYear }}</p>
            </div>
        @endif
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded shadow">
        <div class="p-4 overflow-x-auto">
            <table id="leaveRequestsTable" class="min-w-full table-auto" style="width:100%">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-600" style="width: 50px;">#</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Leave Type</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Start Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">End Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Days</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reason</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Requested</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

<!-- Leave Request Modal -->

@endsection

@section('scripts')
<style>
    /* Align DataTables length and filter controls with Tailwind spacing */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Make selects and inputs match Tailwind controls */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        padding: .5rem .75rem;
        border: 1px solid #e5e7eb;
        border-radius: .375rem;
        background-color: #fff;
        color: #111827;
    }

    /* Give selects extra right padding so native caret doesn't overlap content */
    .dataTables_wrapper .dataTables_length select {
        padding-right: 2.5rem !important;
        padding-left: 0.75rem !important;
        min-width: 70px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 12px;
    }

    /* Slightly reduce header sort caret space so headers line up */
    table.dataTable thead th { position: relative; }
    table.dataTable thead th:after { right: .5rem; }
    
    /* Remove sorting arrow from non-sortable columns */
    table.dataTable thead th.sorting_disabled:before,
    table.dataTable thead th.sorting_disabled:after {
        display: none !important;
    }
    
    /* Style for sortable column headers */
    table.dataTable thead th.sorting,
    table.dataTable thead th.sorting_asc,
    table.dataTable thead th.sorting_desc {
        cursor: pointer;
    }
    
    /* Ensure proper spacing for columns */
    table.dataTable thead th {
        padding-right: 30px !important;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<!-- Load jQuery first (DataTables depends on it) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    let table;
    function initTable(){
        table = $('#leaveRequestsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("employee.leaves.data") }}',
                type: 'GET'
            },
            dom: "<'flex items-center justify-between mb-2'<'left'l><'right'f>>t<'flex items-center justify-between mt-2'<'info'i><'pagination'p>>",
            lengthMenu: [10, 25, 50, 100],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    width: '50px',
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'leave_type'},
                {data: 'start_date'},
                {data: 'end_date'},
                {data: 'days'},
                {data: 'status_badge'},
                {data: 'reason'},
                {data: 'created_at'},
                {data: 'actions', orderable: false, searchable: false},
            ],
            order: [[7, 'desc']], // Sort by created_at desc
            columnDefs: [
                {
                    targets: [0, 8], // #, Actions columns
                    orderable: false
                }
            ],
            createdRow: function(row, data, dataIndex){
                // add small padding classes to TDs
                $(row).find('td').addClass('px-4 py-2');
            }
        });

        // Update stats after table load
        table.on('draw', function() {
            updateStats();
        });

        // Initial stats update
        updateStats();
    }

    function updateStats() {
        // This would typically be an AJAX call to get stats
        // For now, we'll count from the current table data
        var data = table.rows().data();
        var pending = 0, approved = 0, rejected = 0, totalDays = 0;
        
        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            if (row.status_badge && row.status_badge.includes('yellow')) pending++;
            else if (row.status_badge && row.status_badge.includes('green')) {
                approved++;
                totalDays += parseInt(row.days) || 0;
            }
            else if (row.status_badge && row.status_badge.includes('red')) rejected++;
        }
        
        $('#pending-count').text(pending);
        $('#approved-count').text(approved);
        $('#rejected-count').text(rejected);
        $('#days-taken-count').text(totalDays);
    }


    (function attachHandlers(){
        function setup(){
            initTable();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setup);
        } else {
            setup();
        }
    })();
</script>
@endsection
