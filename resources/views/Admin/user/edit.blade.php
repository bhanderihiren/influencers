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

    /* For mobile responsiveness */
    @media (max-width: 768px) {
        .user-details p {
            font-size: 14px;
        }

        .user-details h4 {
            font-size: 20px;
        }

        .row {
            margin-bottom: 10px;
        }
    }
</style>


    @csrf
    <!-- Full Name -->
    
    <input type="hidden" name="id" class="form-control" value="{{ $user->id }}" required>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <label for="full_name"><strong>Full Name:</strong></label>
            <input type="text" id="full_name" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
        </div>
    </div>

    <!-- Email -->
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <label for="email"><strong>Email:</strong></label>
            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
         <div class="col-md-6 col-sm-12">
            <label for="contact_no"><strong>Phone:</strong></label>
            <input type="text" id="contact_no" name="contact_no" class="form-control" value="{{ $user->contact_no }}">
        </div>
    </div>

    <!-- Phone and State -->
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <label for="state"><strong>State:</strong></label>
            <select name="state" id="state" class="form-control" >
                @php  
                    $state = $user->studentInfo->state;
                    echo getStateOptions($state);
                @endphp
            </select>
        </div>
        <div class="col-md-6 col-sm-12">
            <label for="district"><strong>District:</strong></label>
            <select name="district" id="district" class="form-control" >
                @php  
                    $state = $user->studentInfo->state;
                    $district = $user->studentInfo->district;
                    echo getDistrictsbyState($state,$district);
                @endphp
            </select>
        </div>
        <div class="col-md-12 col-sm-12">
            <label for="college_name"><strong>College Name:</strong></label>
            <select name="colleges" id="colleges" class="form-control">
                    @php
                        $district = $user->StudentInfo->district ?? '';
                        $college = $user->StudentInfo->college ?? '';
                        
                        echo $district."<br>";
                        echo $college."<br>";
                        echo getCollegesByDistrictid($district, $college);
                    @endphp
            </select>
        </div>
    </div>
    
    <!-- Principal Contact and HOD Name -->
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <label for="principal_name"><strong>Principal Name:</strong></label>
            <input type="text" id="principal_name" name="principal_name" class="form-control" value="{{ ucfirst($user->studentInfo->principal_name) ?? 'N/A' }}">
        </div>
        <div class="col-md-6 col-sm-12">
            <label for="principal_contact"><strong>Principal Contact:</strong></label>
            <input type="text" id="principal_contact" name="principal_contact" class="form-control" value="{{ ucfirst($user->studentInfo->principal_contact) ?? 'N/A' }}">
        </div>
        <div class="col-md-6 col-sm-12">
            <label for="hod_name"><strong>HOD Name:</strong></label>
            <input type="text" id="hod_name" name="hod_name" class="form-control" value="{{ ucfirst($user->studentInfo->hod_name) ?? 'N/A' }}">
        </div>
    </div>

    <!-- College Information -->
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <label for="course"><strong>Course:</strong></label>
            <select name="course_pursuing" id="course_pursuing" class="form-control" >
                @php 
                    $course = $user->studentInfo->course_pursuing;
                    echo getCourse($course);
                @endphp
            </select>
        </div>
        <div class="col-md-6 col-sm-12">
            <label for="course"><strong>Skill:</strong></label>
            <select name="skill_selection" id="course_pursuing" class="form-control" >
                @php 
                    $skill = $user->studentInfo->skill_selection;
                    echo getSkillOptions($skill);
                @endphp
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <label for="informed_faculty"><strong>Informed Faculty:</strong></label>
            <select id="informed_faculty" name="informed_faculty" class="form-control">
                <option value="1" {{ $user->studentInfo->informed_faculty == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ $user->studentInfo->informed_faculty == 0 ? 'selected' : '' }}>No</option>
                <option value="null" {{ $user->studentInfo->informed_faculty == null ? 'selected' : '' }}>N/A</option>
            </select>
        </div>
        <div class="col-md-6 col-sm-12">
            <label for="status"><strong>Status:</strong></label>
            <select id="status" name="status" class="form-control">
                <option value="1" {{ $user->user_status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ $user->user_status == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
    <!-- Submit Button -->
    <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                </div>
            </div>
    
    
