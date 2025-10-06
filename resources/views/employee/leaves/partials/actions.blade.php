@php
    $canDelete = $leaveRequest->status === 'pending';
    $isRejected = $leaveRequest->status === 'rejected';
    $isApproved = $leaveRequest->status === 'approved';
@endphp

<div class="flex space-x-2">
    @if($canDelete)
        <button onclick="deleteLeaveRequest({{ $leaveRequest->id }})" class="text-red-600 hover:text-red-900 text-sm font-medium">
            Delete
        </button>
    @endif
    
    @if($isRejected)
        <button onclick="showRejectionReason('{{ $leaveRequest->admin_comment ?: 'No specific reason provided by admin.' }}')" class="text-red-600 hover:text-red-900 text-sm font-medium">
            View Rejection Reason
        </button>
    @elseif($isApproved && $leaveRequest->admin_comment)
        <button onclick="showAdminComment('{{ $leaveRequest->admin_comment }}')" class="text-green-600 hover:text-green-900 text-sm font-medium">
            View Admin Comment
        </button>
    @endif
</div>

<script>
function showAdminComment(comment) {
    // Use custom modal instead of alert
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
        max-width: 500px;
        text-align: center;
    `;
    
    content.innerHTML = `
        <div style="font-size: 2rem; margin-bottom: 1rem; color: #10b981;">💬</div>
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #10b981;">Admin Comment</h3>
        <p style="color: #6b7280; margin-bottom: 1.5rem; text-align: left; white-space: pre-wrap;">${comment}</p>
        <button onclick="this.parentElement.parentElement.remove()" style="
            background: #10b981;
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
}

function showRejectionReason(comment) {
    // Custom modal for rejection reasons
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
        max-width: 500px;
        text-align: center;
        border-left: 4px solid #ef4444;
    `;
    
    content.innerHTML = `
        <div style="font-size: 2rem; margin-bottom: 1rem; color: #ef4444;">❌</div>
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #ef4444;">Leave Request Rejected</h3>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1.5rem;">
            <h4 style="font-size: 0.875rem; font-weight: 600; color: #991b1b; margin-bottom: 0.5rem;">Rejection Reason:</h4>
            <p style="color: #7f1d1d; text-align: left; white-space: pre-wrap; margin: 0;">${comment}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" style="
            background: #ef4444;
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
}

function deleteLeaveRequest(leaveRequestId) {
    if (confirm('Are you sure you want to delete this leave request? This action cannot be undone.')) {
        // Show loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Deleting...';
        button.disabled = true;
        
        fetch(`/employee/leaves/${leaveRequestId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
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
                
                content.innerHTML = `
                    <div style="font-size: 2rem; margin-bottom: 1rem; color: #10b981;">✅</div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #10b981;">Success</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">Leave request deleted successfully!</p>
                    <button onclick="this.parentElement.parentElement.remove(); location.reload();" style="
                        background: #10b981;
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
                
                // Auto-reload after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + (data.message || 'Failed to delete leave request'));
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the leave request');
            button.textContent = originalText;
            button.disabled = false;
        });
    }
}
</script>
