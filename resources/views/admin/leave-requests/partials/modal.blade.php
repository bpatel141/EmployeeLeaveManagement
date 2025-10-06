@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal-styles.css') }}">
@endpush

<!-- Approve Modal -->
<div id="approveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50;">
    <div class="max-w-lg mx-auto mt-16 bg-white p-6 rounded shadow-lg" role="dialog" aria-modal="true">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-green-600">Approve Leave Request</h3>
            <button type="button" onclick="closeApproveModal()" class="text-gray-500 hover:text-gray-800">✕</button>
        </div>
        <form id="approveForm">
            <input type="hidden" id="approve_leave_id" />
            <p class="mb-4 text-gray-700">
                Are you sure you want to approve the leave request for <strong id="approve_employee_name"></strong>?
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Comment (Optional)</label>
                <textarea 
                    id="approve_admin_comment" 
                    rows="3" 
                    class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Add any comments..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="approveLeaveRequest()" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed shadow-md" id="approveBtn">
                    <span id="approveText">Approve</span>
                    <span id="approveSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Approving...
                    </span>
                </button>
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:bg-gray-300 disabled:cursor-not-allowed" id="approveCancelBtn">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50;">
    <div class="max-w-lg mx-auto mt-16 bg-white p-6 rounded shadow-lg" role="dialog" aria-modal="true">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-red-600">Reject Leave Request</h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-500 hover:text-gray-800">✕</button>
        </div>
        <form id="rejectForm">
            <input type="hidden" id="reject_leave_id" />
            <p class="mb-4 text-gray-700">
                Are you sure you want to reject the leave request for <strong id="reject_employee_name"></strong>?
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea 
                    id="reject_admin_comment" 
                    rows="3" 
                    class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="rejectLeaveRequest()" class="px-6 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed shadow-md" id="rejectBtn">
                    <span id="rejectText">Reject</span>
                    <span id="rejectSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Rejecting...
                    </span>
                </button>
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:bg-gray-300 disabled:cursor-not-allowed" id="rejectCancelBtn">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/modal-functions.js') }}"></script>
@endpush

