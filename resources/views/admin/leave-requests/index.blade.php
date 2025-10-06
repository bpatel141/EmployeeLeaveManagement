@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Leave Requests Management</h2>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                <select id="statusFilter" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button id="applyStatusFilter" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Apply Filter
                </button>
                <button id="clearStatusFilter" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Clear
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded shadow">
            <div class="p-4 overflow-x-auto">
                <table id="leaveRequestsTable" class="min-w-full table-auto" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600" style="width: 50px;">#</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Employee</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Leave Type</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Start Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">End Date</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">Days</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reason</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">Status</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.leave-requests.partials.modal')
@endsection

@section('scripts')
    <style>
        /* Status Filter Dropdown styling */
        #statusFilter {
            padding: 0.5rem 2.5rem 0.5rem 0.75rem !important;
            min-width: 150px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 12px;
            cursor: pointer;
        }

        /* DataTables styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: .5rem .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .375rem;
            background-color: #fff;
            color: #111827;
        }

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

        table.dataTable thead th {
            position: relative;
            padding-right: 30px !important;
        }

        table.dataTable thead th.sorting_disabled:before,
        table.dataTable thead th.sorting_disabled:after {
            display: none !important;
        }
    </style>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        let table;

        function initTable() {
            table = $('#leaveRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.leave-requests.data") }}',
                    data: function(d) {
                        d.status = document.getElementById('statusFilter').value;
                    }
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
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'employee_name' },
                    { data: 'leave_type_name' },
                    { data: 'start_date' },
                    { data: 'end_date' },
                    { 
                        data: 'days',
                        className: 'text-center'
                    },
                    { 
                        data: 'reason',
                        render: function(data) {
                            return data.length > 50 ? data.substring(0, 50) + '...' : data;
                        }
                    },
                    { 
                        data: 'status_badge',
                        className: 'text-center',
                        orderable: false
                    },
                    { 
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                columnDefs: [
                    {
                        targets: [0, 5, 7, 8],
                        orderable: false
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td').addClass('px-4 py-2');
                },
                order: [[3, 'desc']] // Order by start_date descending
            });
        }

        (function attachHandlers() {
            function setup() {
                initTable();

                // Apply Status Filter
                document.getElementById('applyStatusFilter').addEventListener('click', function() {
                    table.ajax.reload();
                });

                // Clear Status Filter
                document.getElementById('clearStatusFilter').addEventListener('click', function() {
                    document.getElementById('statusFilter').value = '';
                    table.ajax.reload();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setup);
            } else {
                setup();
            }
        })();
    </script>
@endsection

