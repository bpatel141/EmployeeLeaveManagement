<div style="display: flex; gap: 6px; justify-content: center;">
    @if($leaveRequest->status === 'pending')
        <button 
            style="padding: 6px 12px; background-color: #22C55E; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;"
            onmouseover="this.style.backgroundColor='#16A34A'" 
            onmouseout="this.style.backgroundColor='#22C55E'"
            onclick="showApproveModal({{ $leaveRequest->id }}, '{{ $leaveRequest->user->name ?? '' }}')">
            Approve
        </button>
        <button 
            style="padding: 6px 12px; background-color: #EF4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;"
            onmouseover="this.style.backgroundColor='#DC2626'" 
            onmouseout="this.style.backgroundColor='#EF4444'"
            onclick="showRejectModal({{ $leaveRequest->id }}, '{{ $leaveRequest->user->name ?? '' }}')">
            Reject
        </button>
    @else
        <span style="color: #6B7280; font-size: 13px; font-style: italic;">
            {{ ucfirst($leaveRequest->status) }}
        </span>
    @endif
</div>

