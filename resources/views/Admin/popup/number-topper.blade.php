<div class="modal-body">
  <div class="form-group">
      
    <div class="row">
      @if(isset($user) && !empty($user))
        <input type="hidden" name="id" value="{{ isset($user->id)?$user->id:''; }}">
      @endif
    </div>
    
    <!-- State and District -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="state">State</label>
                <select name="state" id="state" class="form-control" required>
                    @php 
                        $state = $user->collageAdmin->state;
                        echo getStateOptions($state); 
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="district-container">
                <label for="district">District</label>
                <select name="district" id="district2" class="form-control">
                    @php
                        $state    = $user->collageAdmin->state;
                        $district = $user->collageAdmin->district;
                        echo getDistrictsbyState($state, $district);
                    @endphp
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group" id="colleges-container">
                <label for="colleges">Colleges</label>
                <select name="colleges" id="colleges" class="form-control">
                     @php
                        $district = $user->collageAdmin->district;
                        $college  = $user->collageAdmin->colleges_id;
                        echo getCollegesByDistrictid($district, $college);
                    @endphp
                </select>
                @error('colleges') 
                    <span class="text-danger">{{ $message }}</span> 
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <label for="first_name">Number of Topper<span class="red-color">*</span></label>
            <input type="text" class="form-control" name="topper_number" id="name" value="{{ isset($user->topper_number)?$user->topper_number:''; }}">
        </div>
    </div>
    
    <!-- Admin Information -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="admin_name">Admin Name</label>
                <input type="text" name="admin_name" id="admin_name" class="form-control" value="{{ isset($user->full_name) ? $user->full_name :'' }}" required>
                @error('admin_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="email" name="admin_email" id="admin_email" class="form-control" value="{{ isset($user->email) ? $user->email :'' }}" required>
                @error('admin_email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Admin Phone Number -->
    <div class="form-group mb-3">
        <label for="admin_phone">Admin Phone Number</label>
        <input type="text" name="admin_phone" id="admin_phone" class="form-control" value="{{ isset($user->contact_no) ? $user->contact_no :'' }}" required>
        @error('admin_phone') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    
  </div>
</div>