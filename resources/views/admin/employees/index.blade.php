@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Employees</h2>
            <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition-colors">Create Employee</a>
        </div>

        <div class="bg-white rounded shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Filter by:</label>
                    <select id="filterType" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Time</option>
                        <option value="yearly">Yearly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    
                    <div id="yearPickerContainer" style="display: none;">
                        <input id="yearPicker" type="text" placeholder="Select Year" 
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               readonly style="cursor: pointer; width: 150px;" />
                    </div>
                    
                    <div id="monthPickerContainer" style="display: none;">
                        <input id="monthPicker" type="text" placeholder="Select Month" 
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               readonly style="cursor: pointer; width: 150px;" />
                    </div>
                    
                    <button id="applyFilter" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        Apply Filter
                    </button>
                    <button id="clearFilter" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition" style="display: none;">
                        Clear
                    </button>
                </div>
                <div class="text-sm text-gray-600 italic">
                    <span id="filterStatus">Showing all employees</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow">
            <div class="p-4 overflow-x-auto">
                <table id="employeesTable" class="min-w-full table-auto" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600" style="width: 50px;">#</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Name</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Email</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Department</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Join Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Highest Leaves</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>


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
        .dataTables_wrapper .dataTables_filter input,
        #filterType,
        #period {
            padding: .5rem .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .375rem;
            background-color: #fff;
            color: #111827;
        }

        /* Give selects extra right padding so native caret doesn't overlap content */
        .dataTables_wrapper .dataTables_length select,
        #filterType {
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
    
    <!-- jQuery UI CSS for Datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Load jQuery first (DataTables depends on it) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        let table;
        function initTable(){
            table = $('#employeesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.employees.data") }}',
                    data: function(d){
                        const filterType = document.getElementById('filterType').value;
                        d.filter = filterType;
                        
                        if (filterType === 'yearly') {
                            d.period = document.getElementById('yearPicker').value;
                        } else if (filterType === 'monthly') {
                            d.period = document.getElementById('monthPicker').value;
                        } else {
                            d.period = '';
                        }
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
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'department'},
                    {data: 'join_date'},
                    {data: 'highest_leaves', orderable: false, searchable: false},
                    {data: 'actions', orderable: false, searchable: false},
                ],
                columnDefs: [
                    {
                        targets: [0, 5, 6], // #, Highest Leaves, Actions columns
                        orderable: false
                    }
                ],
                createdRow: function(row, data, dataIndex){
                    // add small padding classes to TDs
                    $(row).find('td').addClass('px-4 py-2');
                },
                language: {
                    emptyTable: "No employees found with leave requests for the selected period",
                    zeroRecords: "No employees found with leave requests for the selected period"
                }
            });
        }

        (function attachHandlers(){
            function setup(){
                initTable();

                // Initialize Year Picker
                $('#yearPicker').datepicker({
                    dateFormat: 'yy',
                    changeYear: true,
                    showButtonPanel: true,
                    yearRange: '2020:+0',
                    onClose: function(dateText, inst) {
                        const year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).val(year);
                    },
                    beforeShow: function(input, inst) {
                        $('#ui-datepicker-div').addClass('hide-calendar');
                    }
                }).focus(function() {
                    $(".ui-datepicker-month").hide();
                    $(".ui-datepicker-calendar").hide();
                });

                // Initialize Month Picker
                $('#monthPicker').datepicker({
                    dateFormat: 'yy-mm',
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    yearRange: '2020:+0',
                    onClose: function(dateText, inst) {
                        const month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        const year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).val(year + '-' + (String(parseInt(month) + 1).padStart(2, '0')));
                    }
                }).focus(function() {
                    $(".ui-datepicker-calendar").hide();
                });

                // Filter Type Change Handler
                const filterType = document.getElementById('filterType');
                const yearContainer = document.getElementById('yearPickerContainer');
                const monthContainer = document.getElementById('monthPickerContainer');
                const clearBtn = document.getElementById('clearFilter');
                const filterStatus = document.getElementById('filterStatus');

                filterType.addEventListener('change', function(){
                    const value = this.value;
                    
                    // Hide all pickers
                    yearContainer.style.display = 'none';
                    monthContainer.style.display = 'none';
                    clearBtn.style.display = 'none';
                    
                    // Clear values
                    document.getElementById('yearPicker').value = '';
                    document.getElementById('monthPicker').value = '';
                    
                    // Show appropriate picker
                    if (value === 'yearly') {
                        yearContainer.style.display = 'block';
                        filterStatus.textContent = 'Select a year to filter';
                    } else if (value === 'monthly') {
                        monthContainer.style.display = 'block';
                        filterStatus.textContent = 'Select a month to filter';
                    } else {
                        filterStatus.textContent = 'Showing all employees';
                        table.ajax.reload();
                    }
                });

                // Apply Filter Handler
                const apply = document.getElementById('applyFilter');
                if(apply){
                    apply.addEventListener('click', function(){
                        const filterValue = filterType.value;
                        let period = '';
                        
                        if (filterValue === 'yearly') {
                            period = document.getElementById('yearPicker').value;
                            if (!period) {
                                alert('Please select a year');
                                return;
                            }
                            filterStatus.textContent = `Showing employees with leave requests for year ${period}`;
                        } else if (filterValue === 'monthly') {
                            period = document.getElementById('monthPicker').value;
                            if (!period) {
                                alert('Please select a month');
                                return;
                            }
                            filterStatus.textContent = `Showing employees with leave requests for ${period}`;
                        }
                        
                        clearBtn.style.display = filterValue ? 'block' : 'none';
                        table.ajax.reload();
                    });
                }

                // Clear Filter Handler
                const clearFilter = document.getElementById('clearFilter');
                if(clearFilter){
                    clearFilter.addEventListener('click', function(){
                        filterType.value = '';
                        yearContainer.style.display = 'none';
                        monthContainer.style.display = 'none';
                        clearBtn.style.display = 'none';
                        document.getElementById('yearPicker').value = '';
                        document.getElementById('monthPicker').value = '';
                        filterStatus.textContent = 'Showing all employees';
                        table.ajax.reload();
                    });
                }

                // Create Employee Button is now a direct link - no JavaScript needed
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setup);
            } else {
                setup();
            }
        })();
    </script>
@endsection
