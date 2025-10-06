/**
 * Modal Functions for Leave Request Management
 * Handles approve/reject modals and their interactions
 */

// Approve Modal Functions
function showApproveModal(leaveId, employeeName) {
    document.getElementById('approve_leave_id').value = leaveId;
    document.getElementById('approve_employee_name').textContent = employeeName;
    document.getElementById('approve_admin_comment').value = '';
    
    // Reset button states
    const approveBtn = document.getElementById('approveBtn');
    const approveText = document.getElementById('approveText');
    const approveSpinner = document.getElementById('approveSpinner');
    const approveCancelBtn = document.getElementById('approveCancelBtn');
    
    // Reset button to normal state
    approveBtn.disabled = false;
    approveCancelBtn.disabled = false;
    approveText.classList.remove('hidden');
    approveSpinner.classList.add('hidden');
    
    document.getElementById('approveModal').style.display = 'block';
}

function closeApproveModal() {
    // Reset button states before closing
    const approveBtn = document.getElementById('approveBtn');
    const approveText = document.getElementById('approveText');
    const approveSpinner = document.getElementById('approveSpinner');
    const approveCancelBtn = document.getElementById('approveCancelBtn');
    
    approveBtn.disabled = false;
    approveCancelBtn.disabled = false;
    approveText.classList.remove('hidden');
    approveSpinner.classList.add('hidden');
    
    document.getElementById('approveModal').style.display = 'none';
}

function approveLeaveRequest() {
    const approveBtn = document.getElementById('approveBtn');
    const approveText = document.getElementById('approveText');
    const approveSpinner = document.getElementById('approveSpinner');
    const approveCancelBtn = document.getElementById('approveCancelBtn');
    
    // Disable buttons and show loading state
    approveBtn.disabled = true;
    approveCancelBtn.disabled = true;
    approveText.classList.add('hidden');
    approveSpinner.classList.remove('hidden');
    
    const leaveId = document.getElementById('approve_leave_id').value;
    const comment = document.getElementById('approve_admin_comment').value;

    fetch(`/admin/leave-requests/${leaveId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            admin_comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdminAlert(data.message, false);
            closeApproveModal();
            if (typeof table !== 'undefined' && table.ajax) {
                table.ajax.reload();
            }
        } else {
            showAdminAlert(data.message || 'An error occurred', true);
            // Re-enable buttons on error
            approveBtn.disabled = false;
            approveCancelBtn.disabled = false;
            approveText.classList.remove('hidden');
            approveSpinner.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAdminAlert('An error occurred while approving the leave request', true);
        // Re-enable buttons on error
        approveBtn.disabled = false;
        approveCancelBtn.disabled = false;
        approveText.classList.remove('hidden');
        approveSpinner.classList.add('hidden');
    });
}

// Reject Modal Functions
function showRejectModal(leaveId, employeeName) {
    document.getElementById('reject_leave_id').value = leaveId;
    document.getElementById('reject_employee_name').textContent = employeeName;
    document.getElementById('reject_admin_comment').value = '';
    
    // Reset button states
    const rejectBtn = document.getElementById('rejectBtn');
    const rejectText = document.getElementById('rejectText');
    const rejectSpinner = document.getElementById('rejectSpinner');
    const rejectCancelBtn = document.getElementById('rejectCancelBtn');
    
    // Reset button to normal state
    rejectBtn.disabled = false;
    rejectCancelBtn.disabled = false;
    rejectText.classList.remove('hidden');
    rejectSpinner.classList.add('hidden');
    
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    // Reset button states before closing
    const rejectBtn = document.getElementById('rejectBtn');
    const rejectText = document.getElementById('rejectText');
    const rejectSpinner = document.getElementById('rejectSpinner');
    const rejectCancelBtn = document.getElementById('rejectCancelBtn');
    
    rejectBtn.disabled = false;
    rejectCancelBtn.disabled = false;
    rejectText.classList.remove('hidden');
    rejectSpinner.classList.add('hidden');
    
    document.getElementById('rejectModal').style.display = 'none';
}

function rejectLeaveRequest() {
    const rejectBtn = document.getElementById('rejectBtn');
    const rejectText = document.getElementById('rejectText');
    const rejectSpinner = document.getElementById('rejectSpinner');
    const rejectCancelBtn = document.getElementById('rejectCancelBtn');
    
    const leaveId = document.getElementById('reject_leave_id').value;
    const comment = document.getElementById('reject_admin_comment').value;

    if (!comment.trim()) {
        showAdminAlert('Please provide a reason for rejection', true);
        return;
    }

    // Disable buttons and show loading state
    rejectBtn.disabled = true;
    rejectCancelBtn.disabled = true;
    rejectText.classList.add('hidden');
    rejectSpinner.classList.remove('hidden');

    fetch(`/admin/leave-requests/${leaveId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            admin_comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdminAlert(data.message, false);
            closeRejectModal();
            if (typeof table !== 'undefined' && table.ajax) {
                table.ajax.reload();
            }
        } else {
            showAdminAlert(data.message || 'An error occurred', true);
            // Re-enable buttons on error
            rejectBtn.disabled = false;
            rejectCancelBtn.disabled = false;
            rejectText.classList.remove('hidden');
            rejectSpinner.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAdminAlert('An error occurred while rejecting the leave request', true);
        // Re-enable buttons on error
        rejectBtn.disabled = false;
        rejectCancelBtn.disabled = false;
        rejectText.classList.remove('hidden');
        rejectSpinner.classList.add('hidden');
    });
}

// Admin Alert Function
function showAdminAlert(message, isError = false) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        text-align: center;
    `;
    
    const icon = isError ? '❌' : '✅';
    const title = isError ? 'Error' : 'Success';
    const color = isError ? '#ef4444' : '#10b981';
    
    content.innerHTML = `
        <div style="font-size: 2rem; margin-bottom: 1rem;">${icon}</div>
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: ${color};">${title}</h3>
        <p style="color: #6b7280; margin-bottom: 1.5rem;">${message}</p>
        <button onclick="this.parentElement.parentElement.remove()" style="
            background: ${color};
            color: white;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
        ">Close</button>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    // Auto remove after 3 seconds for success messages
    if (!isError) {
        setTimeout(() => {
            if (modal.parentElement) {
                modal.remove();
            }
        }, 3000);
    }
}
