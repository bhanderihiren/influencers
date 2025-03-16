<style type="text/css">
    /* Modal Body Styling */
.modal-body {
    font-family: Arial, sans-serif;
    padding: 20px; 
    color: #333;
}

.user-details {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.user-details h4 {
    font-size: 24px;
    font-weight: 600;
    color: #34495E;
    margin-bottom: 20px;
}

.user-details p {
    font-size: 16px;
    margin-bottom: 10px;
}

.user-details strong {
    font-weight: 600;
    color: #2C3E50;
}

.user-details span {
    font-weight: 400;
    color: #7f8c8d;
}

.row {
    margin-bottom: 15px;
}

.badge {
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 500;
}

.badge-success {
    background-color: #28a745;
    color: #fff;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}
</style>

<div class="modal-body">
    <!-- User Details Section -->
    <div class="user-details">
        <!-- User Info Section -->
        <div class="row">
            <div class="col-md-12">
                <p><strong>Full Name:</strong> <span>{{ $user->full_name }}</span></p>
            </div>
            <div class="col-md-12">
                <p><strong>Email:</strong> <span>{{ $user->email }}</span></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Phone:</strong> <span>{{ $user->contact_no }}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>State:</strong> <span>{{ $user->studentInfo->stateData->name ?? 'N/A' }}</span></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>District:</strong> <span>{{ $user->studentInfo->districtData->name ?? 'N/A' }}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> 
                    <span class="badge {{ $user->user_status == 1 ? 'badge-success' : 'badge-danger' }}">
                        {{ $user->user_status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="row">
            <div class="col-md-6">
                <p><strong>Informed Faculty:</strong> 
                    <span>
                        @if($user->studentInfo->informed_faculty == 1)
                            Yes
                        @elseif($user->studentInfo->informed_faculty == 0)
                            No
                        @else
                            N/A
                        @endif
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Principal Name:</strong> <span>{{ ucfirst($user->studentInfo->principal_name) ?? 'N/A' }}</span></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Principal Contact:</strong> <span>{{ ucfirst($user->studentInfo->principal_contact) ?? 'N/A' }}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>HOD Name:</strong> <span>{{ ucfirst($user->studentInfo->hod_name) ?? 'N/A' }}</span></p>
            </div>
        </div>

        <!-- New Information Fields -->

        <!-- College Information -->
        <div class="row">
            <div class="col-md-12">
                <p><strong>College Name:</strong> <span>{{ $user->studentInfo->collegeData->college_name ?? 'N/A' }}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>College Code:</strong> <span>{{ $user->studentInfo->collegeData->college_code ?? 'N/A' }}</span></p>
            </div>

            <div class="col-md-6">
                <p><strong>Course:</strong> <span>{{ $user->studentInfo->courseData->name ?? 'N/A' }}</span></p>
            </div>

            <div class="col-md-6">
                <p><strong>Skill Set:</strong> <span>{{ $user->studentInfo->skillData->name ?? 'N/A' }}</span></p>
            </div>
        </div>

        <!-- Add more user information fields here if needed -->
    </div>
</div>
