<div style="display: flex; gap: 8px;">
    <a 
        href="{{ route('admin.employees.edit', $user) }}"
        style="padding: 6px 12px; background-color: #3B82F6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;"
        onmouseover="this.style.backgroundColor='#2563EB'" 
        onmouseout="this.style.backgroundColor='#3B82F6'">
        Edit
    </a>
    <button 
        style="padding: 6px 12px; background-color: #EF4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;"
        onmouseover="this.style.backgroundColor='#DC2626'" 
        onmouseout="this.style.backgroundColor='#EF4444'"
        onclick="deleteEmployee({{ $user->id }})">
        Delete
    </button>
</div>

<script>
    // Edit function removed - now using direct links to edit page

    function deleteEmployee(id){
        if(!confirm('Delete this user?')) return;
        
        // Show loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Deleting...';
        button.disabled = true;
        
        fetch('/admin/employees/' + id, {
            method: 'DELETE', 
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showEmployeeAlert(data.message, false);
                
                // Reload DataTable if it exists
                if (typeof table !== 'undefined' && table.ajax) {
                    table.ajax.reload();
                } else {
                    // Fallback to page reload
                    window.location.reload();
                }
            } else {
                showEmployeeAlert(data.message || 'Failed to delete employee', true);
                // Reset button on error
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showEmployeeAlert('An error occurred while deleting the employee', true);
            // Reset button on error
            button.textContent = originalText;
            button.disabled = false;
        });
    }
    
    // Employee Alert Function
    function showEmployeeAlert(message, isError = false) {
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
</script>
